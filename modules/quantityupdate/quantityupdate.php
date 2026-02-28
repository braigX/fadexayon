<?php

if (!defined('_PS_VERSION_')){
  exit;
}

include_once(_PS_MODULE_DIR_.'quantityupdate/classes/quantityUpdateTools.php');

class quantityUpdate extends Module
{

  const IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME = _DB_PREFIX_ . 'mpm_quantityupdate_products_for_update';

  private $_model;
  private $_shopGroupId;
  private $fields_form;
  private $_html;
  private $_shopId;

  public function __construct(){
    include_once(_PS_MODULE_DIR_ . 'quantityupdate/classes/datamodel.php');
    $this->_model = new quantityUpdateModel();

    if( isset(Context::getContext()->shop->id_shop_group) ){
      $this->_shopGroupId = Context::getContext()->shop->id_shop_group;
    }
    elseif( isset(Context::getContext()->shop->id_group_shop) ){
      $this->_shopGroupId = Context::getContext()->shop->id_group_shop;
    }

    $this->_shopId = Context::getContext()->shop->id;
    $this->_html = '';


    $this->name = 'quantityupdate';
    $this->tab = 'quick_bulk_update';
    $this->version = '3.1.4';
    $this->author = 'MyPrestaModules';
    $this->need_instance = 0;
    $this->bootstrap = true;
    $this->module_key = "c7a27e463eb735eb5350a84a2f36780d";

    parent::__construct();

    $this->displayName = $this->l('Mass Product Quantity And Price Update');
    $this->description = $this->l('Mass product quantity and price update is a convenient module especially designed to perform import and export operations with the PrestaShop products and change their mass quantity and price.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  }

  public function install()
  {
    Configuration::updateValue('GOMAKOIL_PRODUCTS_CHECKED', '');
    Configuration::updateValue('GOMAKOIL_MANUFACTURERS_CHECKED', '');
    Configuration::updateValue('GOMAKOIL_SUPPLIERS_CHECKED', '');
    $this->_createTab();

    $create_ids_of_products_for_update_table = Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS ". self::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME . "(
                            id_product int(11) NOT NULL)"
    );

    if (!$create_ids_of_products_for_update_table || !parent::install()) {
      return false;
    }

    return true;
  }

  public function uninstall(){
    $this->_removeTab();
    Configuration::deleteByName('GOMAKOIL_PRODUCTS_CHECKED');
    Configuration::deleteByName('GOMAKOIL_MANUFACTURERS_CHECKED');
    Configuration::deleteByName('GOMAKOIL_SUPPLIERS_CHECKED');
    Configuration::deleteByName('GOMAKOIL_SHOW_NAME_FIELD');
    Configuration::deleteByName('GOMAKOIL_NAME_FIELD');
    Configuration::deleteByName('GOMAKOIL_FORMAT_FILE');

    Db::getInstance()->execute("DROP TABLE IF EXISTS ". self::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME);

    return parent::uninstall();
  }

  public function upgradeTo305()
  {
    return Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS ". self::IDS_OF_PRODUCTS_FOR_UPDATE_TABLE_NAME . "(id_product int(11) NOT NULL)");
  }

  public function upgradeTo313()
  {
      if( file_exists( _PS_MODULE_DIR_ . 'quantityupdate/send.php' ) ){
          unlink(_PS_MODULE_DIR_ . 'quantityupdate/send.php');
      }
      if( file_exists( _PS_MODULE_DIR_ . 'quantityupdate/export.php' ) ){
          unlink(_PS_MODULE_DIR_ . 'quantityupdate/export.php');
      }
      if( file_exists( _PS_MODULE_DIR_ . 'quantityupdate/update.php' ) ){
          unlink(_PS_MODULE_DIR_ . 'quantityupdate/update.php');
      }
      if( file_exists( _PS_MODULE_DIR_ . 'quantityupdate/datamodel.php' ) ){
          unlink(_PS_MODULE_DIR_ . 'quantityupdate/datamodel.php');
      }
      if( file_exists( _PS_MODULE_DIR_ . 'quantityupdate/updateLive.php' ) ){
          unlink(_PS_MODULE_DIR_ . 'quantityupdate/updateLive.php');
      }
    return true;
  }

  private function _createTab()
  {
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminQuantityUpdate';
    $tab->name = array();
    foreach (Language::getLanguages(true) as $lang)
      $tab->name[$lang['id_lang']] = 'Quantity Update';
    $tab->id_parent = -1;
    $tab->module = $this->name;
    $tab->add();
  }

  private function _removeTab()
  {
    $id_tab = (int)Tab::getIdFromClassName('AdminQuantityUpdate');
    if ($id_tab)
    {
      $tab = new Tab($id_tab);
      $tab->delete();
    }
  }

  public function getContent()
  {
	if( Tools::getValue('configure') == 'quantityupdate' ){
      $this->context->controller->addCSS($this->_path.'views/css/style.css');

    if(version_compare(_PS_VERSION_, '1.7.8.0', '>=')){
      $this->context->controller->addCSS($this->_path.'/views/css/quantity_update_style_1780_more.css');
    }

      $this->context->controller->addJS($this->_path.'views/js/main.js');
    }
    $logo = '<img class="logo_myprestamodules" src="../modules/'.$this->name.'/logo.png" />';
    $name = '<h2 id="bootstrap_products">'.$logo.$this->displayName.'</h2>';

    return $name.$this->displayForm().$this->listSettings();
  }

  private function listSettings()
  {
    $fields_list = array(
      'id' => array(
        'title' => $this->l('ID'),
        'type' => 'text',
      ),
      'settings_name' => array(
        'title' => $this->l('Settings Name'),
        'type' => 'text',
      ),
    );

    $helper = new HelperList();
    $helper->shopLinkType = '';
    $helper->simple_header = true;
    $helper->identifier = 'settings_name';
    $helper->actions = array('edit', 'delete');
    $helper->show_toolbar = true;
    $helper->no_link = true;
    $helper->title = $this->l('Settings list');
    $helper->table = 'settings_list';
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    $settings = $this->_model->getSettings();

    return $helper->generateList($settings, $fields_list);
  }

  public function getPath()
  {
    return $_SERVER['REWRITEBASE'] . "modules/quantityupdate/";
  }

  public function displayForm()
  {
    $class = '';
    $name = '';
    $show = 0;
    $format_file = 'xlsx';
    $products = Product::getProducts(Context::getContext()->language->id, 0, 300, 'name', 'asc' );
    $manufacturers = Manufacturer::getManufacturers(false, Context::getContext()->language->id, true, false, false, false, true );
    $suppliers = Supplier::getSuppliers(false, Context::getContext()->language->id);
    $selected_products = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_PRODUCTS_CHECKED'));
    $selected_manufacturers = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_MANUFACTURERS_CHECKED'));
    $selected_suppliers = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_SUPPLIERS_CHECKED'));
    $selected_categories = quantityUpdateTools::jsonDecode(Configuration::get('GOMAKOIL_CATEGORIES_CHECKED'));

    if(Configuration::get('GOMAKOIL_FORMAT_FILE')){
      $format_file =  Configuration::get('GOMAKOIL_FORMAT_FILE');
    }

    $show =  Configuration::get('GOMAKOIL_SHOW_NAME_FIELD');
    $name =  Configuration::get('GOMAKOIL_NAME_FIELD');

    $file_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/quantityupdate/files/';


    $nameDescription = '<p class="available_url">'.$this->l('The file will be available by link below:').'</p>';
    $nameDescription .= '<p><strong><a class="href_export_file"  href="" data-file-url="'.$file_url.'"></a></strong></p>';
    
    if($show){
      $class = ' active_block';
    }

    $field = array(
      array(
        'id' => 'id',
        'name' => $this->l('Product Id')
      ),
      array(
        'id' => 'reference',
        'name' => $this->l('Reference code')
      ),
    );
    $delimiter = array(
      array(
        'id' => ';',
        'name' => ';',
      ),
      array(
        'id' => ':',
        'name' => ':',
      ),
      array(
        'id' => ',',
        'name' => ',',
      ),
      array(
        'id' => '.',
        'name' => '.',
      ),
      array(
        'id' => '/',
        'name' => '/',
      ),
      array(
        'id' => '|.',
        'name' => '|',
      ),
    );

    $round_value = array(
      array(
        'id' => '0',
        'name' => '0',
      ),
      array(
        'id' => '1',
        'name' => '1',
      ),
      array(
        'id' => '2',
        'name' => '2',
      ),
      array(
        'id' => '3',
        'name' => '3',
      ),
      array(
        'id' => '4',
        'name' => '4',
      ),
      array(
        'id' => '5',
        'name' => '5',
      ),
      array(
        'id' => '6',
        'name' => '6',
      ),
    );

    $sort = array(
      array(
        'name' => 'ID',
        'id' => 'id',
      ),
      array(
        'name' => 'Name',
        'id' => 'name',
      ),
      array(
        'name' => 'Price',
        'id' => 'price',
      ),
      array(
        'name' => 'Quantity',
        'id' => 'quantity',
      ),
      array(
        'name' => 'Date add',
        'id' => 'date_add',
      ),
      array(
        'name' => 'Date update',
        'id' => 'date_update',
      )
    );

    $price_increment = array(
      array(
        'name' => 'Percentage',
        'id' => '1',
      ),
      array(
        'name' => 'Amount',
        'id' => '2',
      ),
      array(
        'name' => 'Coefficient',
        'id' => '3',
      ),
      array(
        'name' => 'New value (dont use percentage or amount)',
        'id' => '4',
      ),
    );

    $options_live_update = array(
      array(
        'name' => 'Change only product prices',
        'id' => '1',
      ),
      array(
        'name' => 'Change only combinations impact on price',
        'id' => '2',
      ),
      array(
        'name' => 'Change both',
        'id' => '3',
      ),
    );

    $operations = array(
      array(
        'name' => '+',
        'id' => '1',
      ),
      array(
        'name' => '-',
        'id' => '2',
      ),
      array(
        'name' => '*',
        'id' => '3',
      ),
      array(
        'name' => '/ (Division)',
        'id' => '4',
      ),
    );

    $operations_quantity = array(
      array(
        'name' => '+',
        'id' => '1',
      ),
      array(
        'name' => '-',
        'id' => '2',
      ),
    );


    $quantity_increment = array(
      array(
        'name' => 'Amount',
        'id' => '1',
      ),
      array(
        'name' => 'New value (dont use percentage or amount)',
        'id' => '2',
      ),
    );


    $categories_home = new HelperTreeCategories('associated-categories', $this->l('Select categories'));
    $categories_home->setUseCheckBox(true)
      ->setUseSearch(false);
	   $categories_home->setRootCategory((Shop::getContext() == Shop::CONTEXT_SHOP ? Category::getRootCategory()->id_category : 0));
    $categories_home->setSelectedCategories(array());


    $this->fields_form[0]['form'] = array(
      'tabs' => array(
        'export'    => $this->l('Export'),
        'update'    => $this->l('Update'),
        'live'    => $this->l('Live update'),
        'automatic' => $this->l('Automatic update'),
        'support'   => $this->l('Support'),
        'modules' => $this->l('Related Modules'),
      ),
      'input' => array(
        array(
          'type' => 'html',
          'form_group_class' => 'line_form support_tab_content',
          'tab' => 'live',
          'name' => $this->l('Filter products'),
        ),
        array(
          'type' => 'html',
          'label' => $this->l('Select categories'),
          'name' => 'html_data',
          'form_group_class'=> 'blockfilterCategories',
          'tab' => 'live',
          'html_content' =>  $categories_home->render(),
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'exportFields support_tab_content renderListProducts',
          'tab' => 'live',
          'name' => $this->renderListProducts(),
        ),

        array(
          'type' => 'html',
          'form_group_class' => 'line_form support_tab_content',
          'tab' => 'live',
          'name' => $this->l('Price increment'),
        ),
        array(
          'type' => 'text',
          'label' => $this->l('New price increment:'),
          'name' => 'price_increment',
          'class' => 'price_increment block_style_width_200',
          'tab' => 'live',
          'required' => true,
          'form_group_class' => 'price_increment_block',
          'desc' =>  $this->l('Percentage or amount to increase/decrease'),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Operations (Increase or decrease):'),
          'name' => 'operations',
          'class' => 'operations_increment block_style_width_200',
          'form_group_class' => 'operations_block',
          'tab' => 'live',
          'options' => array(
            'query' =>$operations,
            'id' => 'id',
            'name' => 'name'
          ),
          'desc' =>  $this->l('Percentage or amount to increase/decrease'),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Type of update:'),
          'name' => 'type_price_increment',
          'class' => 'type_price_increment block_style_width_200',
          'form_group_class' => 'price_increment_block',
          'tab' => 'live',
          'options' => array(
            'query' => $price_increment,
            'id' => 'id',
            'name' => 'name'
          ),
            'desc' =>  $this->l('eg: use "Coefficient" if you need: price*2 or price/4' ).'<br>' .$this->l('use "Amount" if you need: price+50$ or price-50$').'<br>'.$this->l(' use "Percentage" if you need: price+20% or price-25%') .'<br>'. $this->l('use "New value" if you need: price=35$'),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Change options:'),
          'name' => 'options_live_update',
          'class' => 'options_live_update block_style_width_200',
          'form_group_class' => 'price_increment_block',
          'tab' => 'live',
          'options' => array(
            'query' => $options_live_update,
            'id' => 'id',
            'name' => 'name'
          ),
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'line_form support_tab_content',
          'tab' => 'live',
          'name' => $this->l('Quantity increment'),
        ),
        array(
          'type' => 'text',
          'label' => $this->l('New quantity increment:'),
          'name' => 'quantity_increment',
          'class' => 'quantity_increment block_style_width_200',
          'tab' => 'live',
          'required' => true,
          'form_group_class' => 'quantity_increment_block',
          'desc' =>  $this->l('Amount to increase/decrease.').'<br>'.$this->l('If product has combinations quantity will be increased for combinations.'),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Operations (Increase or decrease):'),
          'name' => 'quantity_operations',
          'class' => 'quantity_operations_increment block_style_width_200',
          'form_group_class' => 'quantity_operations_block',
          'tab' => 'live',
          'options' => array(
            'query' =>$operations_quantity,
            'id' => 'id',
            'name' => 'name'
          ),
          'desc' =>  $this->l('Amount to increase/decrease'),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Type of update:'),
          'name' => 'type_quantity_increment',
          'class' => 'type_quantity_increment block_style_width_200',
          'form_group_class' => 'quantity_increment_block',
          'tab' => 'live',
          'options' => array(
            'query' => $quantity_increment,
            'id' => 'id',
            'name' => 'name'
          ),
          'desc' =>  $this->l('use "Amount" if you need: quantity+50 or quantity-50').'<br>'. $this->l('use "New value" if you need: quantity=35'),
        ),
        array(
          'type' => 'html',
          'name' => 'html_data',
          'tab' => 'live',
          'form_group_class' => 'button_update_live',
          'html_content' => '<button type="button" class="btn btn-default update_live">'.$this->l('Live update').'</button>'
        ),
        array(
          'type' => 'html',
          'form_group_class' => 'exportFields support_tab_content',
          'tab' => 'support',
          'name' => $this->supportBlock(),
        ),
        array(
          'type' => 'html',
          'tab' => 'modules',
          'form_group_class' => 'support_tab_content',
          'name' => $this->displayTabModules()
        ),
        array(
          'type' => 'radio',
          'label' => $this->l('Select file format:'),
          'name' => 'format_file_update_automatic',
          'required' => true,
          'class' => 'format_file_update_automatic',
          'br' => true,
          'tab' => 'automatic',
          'values' => array(
            array(
              'id' => 'format_xlsx',
              'value' => 'xlsx',
              'label' => $this->l('XLSX')
            ),
            array(
              'id' => 'format_csv',
              'value' => 'csv',
              'label' => $this->l('CSV')
            ),
          )
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Delimiter'),
          'name' => 'delimiter_val',
          'class' => 'delimiter_val',
          'form_group_class' => 'delimiter',
          'tab' => 'automatic',
          'options' => array(
            'query' =>$delimiter,
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'html',
          'tab' => 'automatic',
          'name' => '',
          'form_group_class' => 'line',
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Feed Source'),
          'name' => 'feed_source',
          'class' => 'feed_source',
          'required' => true,
          'form_group_class' => 'feed_source',
          'tab' => 'automatic',
          'options' => array(
            'query' => array(
              array(
                'id' => 'file_url',
                'name' => $this->l('URL')
              ),
              array(
                'id' => 'ftp',
                'name' => $this->l('FTP')
              ),
            ),
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'text',
          'label' => $this->l('File Url:'),
          'name' => 'file_url',
          'class' => 'file_url',
          'tab' => 'automatic',
          'form_group_class' => 'form_file_url',
        ),
        array(
          'type'     => 'text',
          'label'    => $this->l('FTP Server'),
          'tab' => 'automatic',
          'name'     => 'ftp_server',
          'form_group_class' => 'file_import_ftp',
        ),
        array(
          'type'     => 'text',
          'label'    => $this->l('User Name'),
          'tab' => 'automatic',
          'name'     => 'ftp_user',
          'form_group_class' => 'file_import_ftp',
        ),
        array(
          'type'     => 'text',
          'label'    => $this->l('Password'),
          'tab' => 'automatic',
          'name'     => 'ftp_password',
          'form_group_class' => 'file_import_ftp',
        ),
        array(
          'type'     => 'text',
          'label'    => $this->l('Absolute path to file'),
          'tab' => 'automatic',
          'name'     => 'ftp_file_path',
          'form_group_class' => 'file_import_ftp',
        ),
        array(
          'type' => 'html',
          'tab' => 'automatic',
          'form_group_class' => 'form_fields_mapping',
          'name' => '',
          'html_content' => '<button type="button" class="btn btn-default fields_mapping">'.$this->l('Field mapping').'</button>',
        ),
        array(
          'type' => 'html',
          'tab' => 'automatic',
          'name' => '',
          'form_group_class' => 'mapping_form',
        ),
        array(
          'type' => 'radio',
          'label' => $this->l('Select file format:'),
          'name' => 'format_file',
          'required' => true,
          'class' => 'format_file',
          'br' => true,
          'tab' => 'export',
          'form_group_class' => 'export_file_format',
          'values' => array(
            array(
              'id' => 'format_csv',
              'value' => 'csv',
              'label' => $this->l('CSV')
            ),
            array(
              'id' => 'format_xlsx',
              'value' => 'xlsx',
              'label' => $this->l('XLSX')
            )
          )
        ),
        array(
          'type' => 'radio',
          'label' => $this->l('Select file format:'),
          'name' => 'format_file_update',
          'class' => 'format_file',
          'br' => true,
          'tab' => 'update',
          'form_group_class' => 'update_file_format',
          'values' => array(
            array(
              'id' => 'format_csv',
              'value' => 'csv',
              'label' => $this->l('CSV'),
            ),
            array(
              'id' => 'format_xlsx',
              'value' => 'xlsx',
              'label' => $this->l('XLSX')
            )
          )
        ),

        array(
          'type' => 'select',
          'label' => $this->l('Key for product identification'),
          'name' => 'field_for_update',
          'class' => 'field_for_update',
          'form_group_class' => 'key_for_update',
          'tab' => 'update',
          'options' => array(
            'query' =>$field,
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Update price inc. tax'),
          'name' => 'tax_price_update',
          'class' => 'tax_price_update',
          'form_group_class' => 'form_group_class_hide',
          'is_bool' => true,
          'tab' => 'update',
          'values' => array(
            array(
              'id' => 'tax_price_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'tax_price_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        array(
          'type' => 'file',
          'label' => $this->l('File'),
          'name' => 'file',
          'form_group_class' => 'upload_file_update',
          'tab' => 'update',
        ),
        array(
          'type' => 'html',
          'name' => 'html_data',
          'tab' => 'export',
          'form_group_class' => 'form_group_module_hind',
          'html_content' => '<div class="module_hind">' . $this->l('If no filter is selected, module will export all products!') . '</div>'
        ),
        array(
          'type' => 'checkbox_table',
          'name' => 'products[]',
          'class_block' => 'product_list',
          'label' => $this->l('Filter by products:'),
          'class_input' => 'select_products',
          'lang' => true,
          'hint' => '',
          'tab' => 'export',
          'search' => true,
          'display'=> true,
          'values' => array(
            'query' => $products,
            'id' => 'id_product',
            'name' => 'name',
            'value' => $selected_products
          )
        ),
        array(
          'type'  => 'categories',
          'label' => $this->l('Filter by category:'),
          'form_group_class' => 'form_group_category',
          'name'  => 'categories',
          'tab'   => 'export',
          'tree'  => array(
            'id'  => 'categories-tree',
            'use_checkbox' => true,
            'use_search' => true,
            'selected_categories' => $selected_categories ? $selected_categories : array(),
            'root_category' => Shop::getContext() == Shop::CONTEXT_SHOP ? Category::getRootCategory()->id_category : 0
          ),
        ),
        array(
          'type' => 'checkbox_table',
          'name' => 'manufacturers[]',
          'class_block' => 'manufacturer_list',
          'label' => $this->l('Filter by manufacturer:'),
          'class_input' => 'select_manufacturers',
          'lang' => true,
          'hint' => '',
          'tab' => 'export',
          'search' => true,
          'display'=> true,
          'values' => array(
            'query' => $manufacturers,
            'id' => 'id_manufacturer',
            'name' => 'name',
            'value' => $selected_manufacturers
          )
        ),
        array(
          'type' => 'checkbox_table',
          'name' => 'suppliers[]',
          'class_block' => 'supplier_list',
          'label' => $this->l('Filter by suppliers:'),
          'class_input' => 'select_suppliers',
          'lang' => true,
          'hint' => '',
          'tab' => 'export',
          'search' => true,
          'display'=> true,
          'values' => array(
            'query' => $suppliers,
            'id' => 'id_supplier',
            'name' => 'name',
            'value' => $selected_suppliers
          )
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Set specific file name'),
          'name' => 'name_export_file',
          'class' => 'name_export_file',
          'form_group_class' => 'form_group_class_hide',
          'is_bool' => true,
          'tab' => 'export',
          'values' => array(
            array(
              'id' => 'name_export_file_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'name_export_file_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Name for exported file'),
          'name' => 'name_file',
          'tab' => 'export',
          'form_group_class' => 'form_group_name_file'.$class,
        ),
        array(
          'type' => 'html',
          'name' => $nameDescription,
          'tab' => 'export',
          'form_group_class' => 'auto_description'.$class,
        ),


        array(
          'type' => 'html',
          'tab' => 'export',
          'form_group_class' => 'form_group_line',
          'html_content' => ' ',
          'name' => ' ',
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Number of decimal points'),
          'name' => 'round_value',
          'class' => 'round_value',
          'tab' => 'export',
          'form_group_class' => 'form_group_class_hide',
          'options' => array(
            'query' =>$round_value,
            'id' => 'id',
            'name' => 'name'
          ),
          'desc' =>  $this->l('Will be used in the prices. You can choose to have 5.12 instead of 5.121123.'),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Export price inc. tax'),
          'name' => 'tax_price',
          'class' => 'tax_price',
          'form_group_class' => 'form_group_class_hide',
          'is_bool' => true,
          'tab' => 'export',
          'values' => array(
            array(
              'id' => 'tax_price_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'tax_price_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Export product image cover'),
          'name' => 'image_cover',
          'class' => 'image_cover',
          'form_group_class' => 'form_group_class_hide form_image_cover',
          'is_bool' => true,
          'tab' => 'export',
          'values' => array(
            array(
              'id' => 'image_cover_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'image_cover_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        array(
          'type' => 'switch',
          'label' => $this->l('Only active products'),
          'name' => 'active_products',
          'class' => 'active_products',
          'form_group_class' => 'form_group_class_hide',
          'is_bool' => true,
          'tab' => 'export',
          'values' => array(
            array(
              'id' => 'active_products_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'active_products_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        
        array(
          'type' => 'switch',
          'label' => $this->l('Only inactive products'),
          'name' => 'inactive_products',
          'class' => 'inactive_products',
          'form_group_class' => 'form_group_class_hide',
          'is_bool' => true,
          'tab' => 'export',
          'values' => array(
            array(
              'id' => 'inactive_products_on',
              'value' => 1,
              'label' => $this->l('Enabled')
            ),
            array(
              'id' => 'inactive_products_off',
              'value' => 0,
              'label' => $this->l('Disabled')
            )
          ),
        ),
        array(
          'type' => 'html',
          'label' => $this->l('Products with price'),
          'form_group_class' => 'form_group_class_hide',
          'tab' => 'export',
          'name' => $this->priceSelection(),
        ),
        array(
          'type' => 'html',
          'label' => $this->l('Products with quantity'),
          'form_group_class' => 'form_group_class_hide',
          'tab' => 'export',
          'name' => $this->quantitySelection(),
        ),
        array(
          'type' => 'select',
          'label' => $this->l('Sort by'),
          'name' => 'orderby',
          'class' => 'orderby',
          'tab' => 'export',
          'form_group_class' => 'form_group_class_hide',
          'options' => array(
            'query' =>$sort,
            'id' => 'id',
            'name' => 'name'
          )
        ),
        array(
          'type' => 'radio',
          'label' => $this->l(' '),
          'name' => 'orderway',
          'tab' => 'export',
          'required' => true,
          'form_group_class' => 'form_group_class_hide sort_block_orderway',
          'br' => true,
          'values' => array(
            array(
              'id' => 'orderway_asc',
              'value' => 'asc',
              'label' => $this->l('ASC')
            ),
            array(
              'id' => 'orderway_desc',
              'value' => 'desc',
              'label' => $this->l('DESC')
            )
          )
        ),
        array(
          'type' => 'hidden',
          'name' => 'token_quantityupdate',
        ),
        array(
          'type' => 'hidden',
          'name' => 'id_shop',
        ),
        array(
          'type' => 'hidden',
          'name' => 'id_lang',
        ),
        array(
          'type' => 'hidden',
          'name' => 'quantity_update_token',
        ),
        array(
          'type' => 'html',
          'name' => 'html_data',
          'tab' => 'export',
          'form_group_class' => 'button_export_block',
          'html_content' => '<button type="button" class="btn btn-default export">'.$this->l('Export').'</button>'
        ),
        array(
          'type' => 'html',
          'name' => 'html_data',
          'form_group_class' => 'button_update_block',
          'tab' => 'update',
          'html_content' => '<button type="button" class="btn btn-default update">'.$this->l('Update').'</button>'
        ),
      ),
    );

    $helper = new HelperForm();
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->fields_value['token_quantityupdate'] = Tools::getAdminTokenLite('AdminQuantityUpdate');
    $helper->fields_value['id_shop'] = $this->_shopId;
    $helper->fields_value['id_lang'] = Context::getContext()->language->id;
    $helper->fields_value['quantity_update_token'] = Tools::getAdminTokenLite('AdminQuantityUpdate');
    $helper->fields_value['format_file'] = $format_file;
    $helper->fields_value['format_file_update'] = 'xlsx';
    $helper->fields_value['format_file_update_automatic'] = 'xlsx';
    $helper->fields_value['delimiter_val'] = ';';
    $helper->fields_value['file_url'] = '';
    $helper->fields_value['ftp_server'] = '';
    $helper->fields_value['type_price_increment'] = 1;
    $helper->fields_value['operations'] = 1;
    $helper->fields_value['price_increment'] = '';
    $helper->fields_value['options_live_update'] = 1;
    $helper->fields_value['type_quantity_increment'] = 1;
    $helper->fields_value['quantity_operations'] = 1;
    $helper->fields_value['quantity_increment'] = '';
    $helper->fields_value['ftp_user'] = '';
    $helper->fields_value['ftp_password'] = '';
    $helper->fields_value['ftp_file_path'] = '';
    $helper->fields_value['feed_source'] = 'file_url';
    $helper->fields_value['field_for_update'] = 'id';
    $helper->fields_value['search_field'] = '';
    $helper->fields_value['active_products'] = 0;
    $helper->fields_value['round_value'] = 2;
    $helper->fields_value['image_cover'] = 1;
    $helper->fields_value['orderby'] = 1;
    $helper->fields_value['orderway'] = 'asc';
    $helper->fields_value['tax_price'] = 0;
    $helper->fields_value['tax_price_update'] = 0;
    $helper->fields_value['inactive_products'] = 0;
    $helper->fields_value['name_file'] = $name;
    $helper->fields_value['name_export_file'] = $show;
    $helper->fields_value['price'] = 0;

    return $helper->generateForm($this->fields_form);
  }

  public function priceSelection(){

    return $this->display(__FILE__, 'views/templates/hook/blockSelectionPrice.tpl');
  }

  public function supportBlock(){
    return $this->display(__FILE__, "views/templates/hook/supportForm.tpl");
  }

  public function displayTabModules(){
    return $this->display(__FILE__, 'views/templates/hook/modules.tpl');
  }

  public function quantitySelection(){

    return $this->display(__FILE__, 'views/templates/hook/blockSelectionQuantity.tpl');
  }

  public function searchProducts($search,$id_shop, $id_lang)
  {
    $name_config = 'GOMAKOIL_PRODUCTS_CHECKED';
    $products = $this->_model->searchProduct($id_shop, $id_lang, $search);
    $products_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    $this->context->smarty->assign(
      array(
        'data'        => $products,
        'items_check' => $products_check,
        'name'        => 'products[]',
        'id'          => 'id_product',
        'title'       => 'name',
        'class'       => 'select_products'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function searchManufacturers($search)
  {
    $name_config = 'GOMAKOIL_MANUFACTURERS_CHECKED';
    $items = $this->_model->searchManufacturer($search);
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'manufacturers[]',
        'id'          => 'id_manufacturer',
        'title'       => 'name',
        'class'       => 'select_manufacturers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function searchSuppliers($search)
  {
    $name_config = 'GOMAKOIL_SUPPLIERS_CHECKED';
    $items = $this->_model->searchSupplier($search);
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'suppliers[]',
        'id'          => 'id_supplier',
        'title'       => 'name',
        'class'       => 'select_suppliers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showCheckedProducts($id_shop, $id_lang)
  {
    $name_config = 'GOMAKOIL_PRODUCTS_CHECKED';
    $products_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    if( !$products_check ){
      $products_check = "";
    }
    $products = $this->_model->showCheckedProducts($id_shop, $id_lang, $products_check);
    $this->context->smarty->assign(
      array(
        'data'        => $products,
        'items_check' => $products_check,
        'name'        => 'products[]',
        'id'          => 'id_product',
        'title'       => 'name',
        'class'       => 'select_products'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showCheckedManufacturers()
  {
    $name_config = 'GOMAKOIL_MANUFACTURERS_CHECKED';
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    if( !$items_check ){
      $items_check = "";
    }
    $items = $this->_model->showCheckedManufacturers($items_check);
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'manufacturers[]',
        'id'          => 'id_manufacturer',
        'title'       => 'name',
        'class'       => 'select_manufacturers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showCheckedSuppliers()
  {
    $name_config = 'GOMAKOIL_SUPPLIERS_CHECKED';
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    if( !$items_check ){
      $items_check = "";
    }
    $items = $this->_model->showCheckedSuppliers($items_check);
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'suppliers[]',
        'id'          => 'id_supplier',
        'title'       => 'name',
        'class'       => 'select_suppliers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showAllProducts($id_shop, $id_lang)
  {
    $name_config = 'GOMAKOIL_PRODUCTS_CHECKED';
    $products_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    $products = $this->_model->showCheckedProducts($id_shop, $id_lang, false);
    $this->context->smarty->assign(
      array(
        'data'        => $products,
        'items_check' => $products_check,
        'name'        => 'products[]',
        'id'          => 'id_product',
        'title'       => 'name',
        'class'       => 'select_products'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showAllManufacturers()
  {
    $name_config = 'GOMAKOIL_MANUFACTURERS_CHECKED';
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    if( !$items_check ){
      $items_check = "";
    }
    $items = $this->_model->showCheckedManufacturers(false);
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'manufacturers[]',
        'id'          => 'id_manufacturer',
        'title'       => 'name',
        'class'       => 'select_manufacturers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function showAllSuppliers()
  {
    $name_config = 'GOMAKOIL_SUPPLIERS_CHECKED';
    $items_check = quantityUpdateTools::jsonDecode(Configuration::get($name_config));
    if( !$items_check ){
      $items_check = "";
    }
    $items = $this->_model->showCheckedSuppliers(false);
    $this->context->smarty->assign(
      array(
        'data'        => $items,
        'items_check' => $items_check,
        'name'        => 'suppliers[]',
        'id'          => 'id_supplier',
        'title'       => 'name',
        'class'       => 'select_suppliers'
      )
    );
    return $this->display(__FILE__, 'views/templates/hook/filterForm.tpl');
  }

  public function searchProductsList($id_lang, $id_shop, $search_data){

    $search = '';

    $p = $search_data['p'];
    $n = $search_data['n'];


    if($search_data['id']){
      $search .= " AND (p.id_product LIKE '%".pSQL($search_data['id'])."%') ";
    }

    if($search_data['reference']){
      $search .= " AND (p.reference LIKE '%".pSQL($search_data['reference'])."%') ";
    }

    if($search_data['name']){
      $search .= " AND (pl.name LIKE '%".pSQL($search_data['name'])."%') ";
    }

    if($search_data['supplier']){
      $search .= " AND (s.id_supplier LIKE '%".pSQL($search_data['supplier'])."%') ";
    }

    if($search_data['manufacturer']){
      $search .= " AND (m.id_manufacturer LIKE '%".pSQL($search_data['manufacturer'])."%') ";
    }

    if( $search_data['categories'] ){
      $categories = implode(",", $search_data['categories']);
      $search .= " AND cp.id_category IN (".pSQL($categories).") ";
    }
    
    if( ($search_data['price_min'] !== "") && ($search_data['price_max'] !== "")){

      if($search_data['price_min']<0){
        $search_data['price_min'] = 0;
      }

      if($search_data['price_max']<0){
        $search_data['price_max'] = 0;
      }

      if($search_data['price_min'] > $search_data['price_max']){
        $search_data['price_max'] = $search_data['price_min'];
      }

      if($search_data['price_min'] <= $search_data['price_max']){
        $search .= " AND ( ps.price <= ". (float)$search_data['price_max'] . " AND ps.price >= ". (float)$search_data['price_min'] . ") ";
      }

    }

    if( ($search_data['price_min'] !== "") && ($search_data['price_max'] == "")){
      $search .= " AND (ps.price >= ". (float)$search_data['price_min'] . ") ";
    }

    if( ($search_data['price_max'] !== "") && ($search_data['price_min'] == "")){
      $search .= " AND (ps.price <= ". (float)$search_data['price_max'] . ") ";
    }


    if( ($search_data['quantity_min'] !== "") && ($search_data['quantity_max'] !== "")){

      if($search_data['quantity_min']<0){
        $search_data['quantity_min'] = 0;
      }

      if($search_data['quantity_max']<0){
        $search_data['quantity_max'] = 0;
      }

      if($search_data['quantity_min'] > $search_data['quantity_max']){
        $search_data['quantity_max'] = $search_data['quantity_min'];
      }

      if($search_data['quantity_min'] <= $search_data['quantity_max']){
        $search .= " AND ( sa.quantity <= ". (float)$search_data['quantity_max'] . " AND sa.quantity >= ". (float)$search_data['quantity_min'] . ") ";
      }

    }

    if( ($search_data['quantity_min'] !== "") && ($search_data['quantity_max'] == "")){
      $search .= " AND (sa.quantity >= ". (float)$search_data['quantity_min'] . ") ";
    }

    if( ($search_data['quantity_max'] !== "") && ($search_data['quantity_min'] == "")){
      $search .= " AND (sa.quantity <= ". (float)$search_data['quantity_max'] . ") ";
    }



    if($search_data['active'] && ($search_data['active'] !== "")){

      if($search_data['active'] == 1){
        $active = 1;
      }
      if($search_data['active'] == 2){
        $active = 0;
      }

      $search .= " AND (p.active LIKE '%".pSQL($active)."%') ";

    }


    return $this->renderList($p, $n, $search, $id_lang, $id_shop, $search_data);

  }


  public function renderListProducts(){

    $search = '';

    $n = 50;
    $p = 1;

    $search_data = array(
      'p' => 1,
      'n' => 50,
      'categories' => array(),
      'id' => '',
      'reference' => '',
      'name' => '',
      'supplier' => '',
      'manufacturer' => '',
      'price_min' => '',
      'price_max' => '',
      'quantity_min' => '',
      'quantity_max' => '',
      'active' => '',
      'products' => array(),
    );

    $this->_html .= $this->renderList($p, $n, $search, Context::getContext()->language->id, Context::getContext()->shop->id, $search_data);

    return $this->_html;
  }

  public function renderList($p, $n, $search, $id_lang, $id_shop, $search_data)
  {



    $limit = ($p-1)*(int)$n.','.$n;

    $list_supplier = array();
    $list_manufacturer = array();
    $list = array();

    $categories = $this->getCategories($id_shop, $id_lang);
    $manufacturers = Manufacturer::getManufacturers(false, $id_lang, true, false, false, false, true );
    $suppliers = Supplier::getSuppliers(false, $id_lang, true, false, false, false, true );

    if($suppliers){
      foreach ($suppliers as $supplier){
        $list_supplier[$supplier['id_supplier']] = $supplier['name'];
      }
    }

    if($manufacturers){
      foreach ($manufacturers as $manufacturer){
        $list_manufacturer[$manufacturer['id_manufacturer']] = $manufacturer['name'];
      }
    }

    if($categories){
      foreach ($categories as $category){
        $list[$category['id_category']] = $category['name'];
      }
    }

    $products = $this->getProducts($search, $id_shop, $id_lang, $limit);
    $all = $this->getProducts($search, $id_shop, $id_lang);

    if($all){
      $all = count($all);
    }


    $fields_list = array(
      'id_product' => array(
        'title' => $this->l('ID'),
        'search' => true,
        'width'  => 40,
        'search_value'  => $search_data['id'],
      ),
      'image_link' => array(
        'title' => $this->l('Image'),
      ),
      'reference' => array(
        'title' => $this->l('Reference'),
        'search' => true,
        'search_value'  => $search_data['reference'],
      ),
      'name' => array(
        'title' => $this->l('Product name'),
        'search' => true,
        'search_value'  => $search_data['name'],
      ),
      'name_category' => array(
        'title' => $this->l('Category default'),
        'search' => false,
        'search_value'  => '',
      ),
      'supplier' => array(
        'title' => $this->l('Supplier default'),
        'search' => true,
        'type' => 'select',
        'list' => $list_supplier,
        'search_value'  => $search_data['supplier'],
      ),
      'manufacturer' => array(
        'title' => $this->l('Manufacturer'),
        'search' => true,
        'type' => 'select',
        'list' => $list_manufacturer,
        'search_value'  => $search_data['manufacturer'],
      ),
      'price' => array(
        'title' => $this->l('Price (tax excl.)'),
        'search' => true,
        'type' => 'min_max',
        'search_value'  => array(
          'min' => $search_data['price_min'],
          'max' => $search_data['price_max'],
        ),
      ),
      'quantity' => array(
        'title' => $this->l('Quantity'),
        'search' => true,
        'type' => 'min_max',
        'search_value'  => array(
          'min' => $search_data['quantity_min'],
          'max' => $search_data['quantity_max'],
        ),
      ),
      'active' => array(
        'title' => $this->l('Status'),
        'active' => 'status',
        'type' => 'bool',
        'align' => 'center',
        'search' => true,
        'search_value'  => $search_data['active'],
      ),
    );


    $productNb = (int)$all;
    $pages_nb = ceil($productNb/$n);

    if( $pages_nb > 5 && $p > 3){
      $stop = 2 + $p;
    }
    elseif( $pages_nb > 5){
      $stop = 5;
    }
    else{
      $stop = $pages_nb;
    }
    if( $p == $pages_nb || $p == ($pages_nb + 1) ){
      $stop = $p;
    }
    if( ($p +1) == $pages_nb ){
      $stop = $p + 1;
    }
    $start = 1;
    if( $p >= 5 ){
      $start = $p - 2;
    }


    $selected_products = array();
    if($search_data['products']){
      foreach ($search_data['products'] as $value){
        $selected_products[$value] = 1;
      }
    }

    //Add images links
    foreach ($products as $product_key => $product) {
      $product_obj = new Product($product['id_product']);
      $cover_link = '';
      $cover = Image::getCover($product_obj->id);

      if ($cover) {
        $cover_link = Context::getContext()->link->getImageLink($product_obj->link_rewrite[$id_lang], $cover['id_image'], 'small_default');
      }

      $products[$product_key]['image_link'] = $cover_link;
    }

    $this->context->smarty->assign(
      array(
        'fields_list'       => $fields_list,
        'products'          => $products,
        'stop'              => $stop,
        'start'             => $start,
        'count'             => $all,
        'n'                 => $n,
        'pages_nb'          => $pages_nb,
        'selected_products' => $selected_products,
        'p'                 => $p,
        'path_pagination'   => '',
      )
    );

    return $this->display(__FILE__, 'views/templates/hook/productsList.tpl');
  }


  public function displayPriceTable($price)
  {
    return Tools::convertPrice($price);
  }


  public function getCategories( $id_shop = false, $id_lang  = false)
  {
    if($id_shop === false){
      $id_shop =  $this->context->shop->id ;
    }
    if($id_lang === false){
      $id_lang =  $this->context->language->id ;
    }

    $sql = '
			SELECT c.id_category, cl.name
      FROM ' . _DB_PREFIX_ . 'category as c
      LEFT JOIN ' . _DB_PREFIX_ . 'category_lang as cl
      ON c.id_category = cl.id_category 
      WHERE cl.id_lang = ' . (int)$id_lang . '
      AND cl.id_shop = ' . (int)$id_shop . '   
			';
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }


  public function getProducts( $search, $id_shop = false, $id_lang  = false, $limit = false)
  {
    $limit_res = '';
    if($id_shop === false){
      $id_shop =  $this->context->shop->id ;
    }
    if($id_lang === false){
      $id_lang =  $this->context->language->id ;
    }
    if($limit){
      $limit_res =  ' LIMIT '.$limit;
    }

    $sql = '
			SELECT p.id_product, pl.name, p.reference, p.active, cl.name as name_category, sa.quantity, ps.price, s.name as supplier, m.name as manufacturer, s.id_supplier, m.id_manufacturer
      FROM ' . _DB_PREFIX_ . 'product_lang as pl
      LEFT JOIN ' . _DB_PREFIX_ . 'product as p
      ON p.id_product = pl.id_product
      LEFT JOIN ' . _DB_PREFIX_ . 'product_shop as ps
      ON p.id_product = ps.id_product
      LEFT JOIN ' . _DB_PREFIX_ . 'category_product as cp
      ON p.id_product = cp.id_product
      LEFT JOIN ' . _DB_PREFIX_ . 'category_lang as cl
      ON p.id_category_default = cl.id_category 
      LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer as m
      ON p.id_manufacturer = m.id_manufacturer
      LEFT JOIN ' . _DB_PREFIX_ . 'supplier as s
      ON p.id_supplier = s.id_supplier 
      LEFT JOIN ' . _DB_PREFIX_ . 'stock_available as sa
      ON sa.id_product = p.id_product AND sa.id_product_attribute = 0
      WHERE pl.id_lang = ' . (int)$id_lang . '
      AND pl.id_shop = ' . (int)$id_shop . '
      AND cl.id_lang = ' . (int)$id_lang . '
      AND cl.id_shop = ' . (int)$id_shop . '
      AND ps.id_shop = ' . (int)$id_shop . '
      ' . $search . '
      GROUP BY p.id_product
      ' . $limit_res . '
			';


    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }


}
