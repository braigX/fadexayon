<?php
/**
* 2018 Prestamatic
*
* NOTICE OF LICENSE
*
*  @author    Prestamatic
*  @copyright 2018 Prestamatic
*  @license   Licensed under the terms of the MIT license
*  @link      https://prestamatic.co.uk
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomShippingRate extends CarrierModule
{
    public $id_carrier;
    
    public $default_config;

    protected $adminTabs = array(
        array(
            'class_name' => 'AdminCustomShippingRate',
            'parent' => 'AdminParentCustomer',
            'name' => 'Shipping Quote Requests'
        ),
    );

    public function __construct()
    {
        $this->name = 'customshippingrate';
        $this->tab = 'shipping_logistics';
        $this->version = '2.1.8';
        $this->author = 'Prestamatic';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '34933154a8bb492125417b56702b05bd';

        parent::__construct();

        $this->displayName = $this->l('Custom Shipping Rate');
        $this->description = $this->l('Allows a custom shipping rate to be applied
        in the back office when creating orders');
        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);

        if (self::isInstalled($this->name)) {
            // Getting carrier list
            $carriers = Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            );

            // Saving id carrier list
            $id_carrier_list = array();
            foreach ($carriers as $carrier) {
                $id_carrier_list[] = $carrier['id_carrier'];
            }
            // Testing if Carrier Id exists
            $warning = array();
            if (!in_array((int)(Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')), $id_carrier_list)) {
                $warning[] = $this->l('"Custom Shipping Carrier"').' ';
            }
            if (count($warning)) {
                $this->warning .= implode(' , ', $warning).$this->l('must be configured to use this module
                correctly').' ';
            }
        }

        $zone_list = array();
        $zones = Zone::getZones(true);
        if ($zones) {
            foreach ($zones as $zone) {
                $zone_list[] = $zone['id_zone'];
            }
        }
        $this->default_config = array(
            'CUSTOM_SHIPPING_CARRIER_ID' => '0',
            'CUSTOMSHIP_ID_CONTACT' => '0',
            'CUSTOMSHIP_EMAIL_TO' => Configuration::get('PS_SHOP_EMAIL'),
            'CUSTOMSHIP_QUOTE_EXPIRES' => '15',
            'CUSTOMSHIP_CARRIERS' => '',
            'CUSTOMSHIP_MIN_WEIGHT' => '0',
            /* Add with team wassim novatis*/
            'CUSTOMSHIP_MIN_WIDTH' => '0',
            'CUSTOMSHIP_MIN_HEIGHT' => '0',
            /* End*/
            'CUSTOMSHIP_MAX_WEIGHT' => '0',
            'CUSTOMSHIP_MIN_PRICE' => '0',
            'CUSTOMSHIP_MAX_PRICE' => '0',
            'CUSTOMSHIP_ID_TAX_RULES_GROUP' => '0',
            'CUSTOMSHIP_CARRIER_ZONE_LIST' => json_encode($zone_list),
            'CUSTOMSHIP_AUTO_CLEAN' => '0',
        );
    }

    public static function createCustomShippingRateTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customshippingrate`(
            `id_customshippingrate` int(10) unsigned NOT NULL auto_increment,
            `id_cart` INT(10) UNSIGNED NOT NULL UNIQUE,
            `id_customer` INT(10) UNSIGNED NOT NULL,
            `id_address_delivery` INT(10) UNSIGNED NOT NULL,
            `carrier_name` varchar(255) NOT NULL default \'\',
            `carrier_delay` varchar(255) NOT NULL default \'\',
            `shipping_price` decimal(17,2) NOT NULL,
            `number_products` INT(10) UNSIGNED NOT NULL,
            `total_weight` decimal(17,2) NOT NULL,
            `order_total` decimal(17,2) NOT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_customshippingrate`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return true;
    }

    public static function dropCustomShippingRateTable()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'customshippingrate`';
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return true;
    }

    public function install()
    {
        if (!parent::install() || !$this->createCustomShippingRateTable()
        || !$this->installMails()
        || !$this->installDefaultConfig()
        || !$this->installAdminTab()
        || !$this->installExternalCarrier()
        || !$this->registerHook('displayHeader')
        || !$this->registerHook('displayBackOfficeHeader')
        || !$this->registerHook('actionAdminControllerSetMedia')
        || !$this->registerHook('displayBeforeCarrier')
        || !$this->registerHook('actionCarrierUpdate')
        || !$this->registerHook('actionValidateOrder')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        // Uninstall
        if (!parent::uninstall() || !$this->dropCustomShippingRateTable()
        || !$this->unregisterHook('displayHeader')
        || !$this->unregisterHook('displayBackOfficeHeader')
        || !$this->unregisterHook('actionAdminControllerSetMedia')
        || !$this->unregisterHook('displayBeforeCarrier')
        || !$this->unregisterHook('actionCarrierUpdate')
        || !$this->unregisterHook('actionValidateOrder')) {
            return false;
        }

        // Remove Tabs
        foreach ($this->adminTabs as $tab) {
            $idTab = Tab::getIdFromClassName($tab['class_name']);
            if ($idTab) {
                $_tab = new Tab($idTab);
                $_tab->delete();
            }
        }

        // Delete External Carrier
        $Carrier1 = new Carrier((int)(Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')));

        // If external carrier is default set other one as default
        if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($Carrier1->id)) {
            $carriersD = Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            );
            foreach ($carriersD as $carrierD) {
                if ($carrierD['active'] and !$carrierD['deleted'] and
                ($carrierD['name'] != $Carrier1->name)) {
                    Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
                }
            }
        }

        // Then delete Carrier
        $Carrier1->deleted = 1;
        if (!$Carrier1->update()) {
            return false;
        }
        return true;
    }

    public function installDefaultConfig()
    {
        $return = true;
        if ($this->getDefaultConfig()) {
            foreach ($this->getDefaultConfig() as $key => $value) {
                if (!Configuration::updateValue($key, $value, true)) {
                    $return = false;
                }
            }
        }
        return $return;
    }

    public function installAdminTab()
    {
        $languages = Language::getLanguages();
        foreach ($this->adminTabs as $tab) {
            $_tab = new Tab();
            $_tab->class_name = $tab['class_name'];
            $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
            $_tab->module = $this->name;
            foreach ($languages as $language) {
                if ($language['iso_code'] == 'en') {
                    $_tab->name[$language['id_lang']] = $tab['name'];
                } elseif ($language['iso_code'] == 'gb') {
                    $_tab->name[$language['id_lang']] = $tab['name'];
                } elseif ($language['iso_code'] == 'fr') {
                    $_tab->name[$language['id_lang']] = 'Demandes de devis d\'expédition';
                } elseif ($language['iso_code'] == 'de') {
                    $_tab->name[$language['id_lang']] = 'Versandanfrage';
                } elseif ($language['iso_code'] == 'es') {
                    $_tab->name[$language['id_lang']] = 'Solicitudes de cotización de envío';
                } elseif ($language['iso_code'] == 'it') {
                    $_tab->name[$language['id_lang']] = 'Richieste di preventivo di spedizione';
                } elseif ($language['iso_code'] == 'nl') {
                    $_tab->name[$language['id_lang']] = 'Verzendofferte aanvragen';
                } else {
                    $_tab->name[$language['id_lang']] = $tab['name'];
                }
            }
            if ($_tab->add()) {
                return true;
            }
        }
        return false;
    }

    public function installExternalCarrier()
    {
        $carrier = new Carrier();
        $carrier->name = $this->l('Custom Shipping Rate');
        $carrier->id_tax_rules_group = 0;
        $carrier->id_zone = 1;
        $carrier->active = true;
        $carrier->deleted = 0;
        $carrier->shipping_handling = false;
        $carrier->range_behavior = 0;
        $carrier->is_module = true;
        $carrier->shipping_external = true;
        $carrier->external_module_name = $this->name;
        $carrier->need_range = true;
        $carrier->grade = 9;

        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $carrier->delay[(int)$language['id_lang']] = $this->l('Custom');
        }

        if ($carrier->add()) {
            Configuration::updateValue('CUSTOM_SHIPPING_CARRIER_ID', (int)$carrier->id);
            if (_PS_VERSION_ > 1.6) {
                $carrier_img = 'carrier.jpg';
            } else {
                $carrier_img = 'carrier16.jpg';
            }

            if (is_callable(array('Tools', 'copy'))) {
                Tools::copy(dirname(__FILE__).'/views/img/'.$carrier_img, _PS_SHIP_IMG_DIR_.'/'
                .(int)$carrier->id.'.jpg');
            } else {
                @copy(dirname(__FILE__).'/views/img/'.$carrier_img, _PS_SHIP_IMG_DIR_.'/'
                .(int)$carrier->id.'.jpg');
            }

            $groups = Group::getGroups(true);
            foreach ($groups as $group) {
                Db::getInstance()->insert('carrier_group', array('id_carrier' => (int)($carrier->id),
                'id_group' => (int)($group['id_group'])));
            }

            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '10000';
            $rangePrice->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '10000';
            $rangeWeight->add();

            $zones = Zone::getZones(true);
            foreach ($zones as $zone) {
                Db::getInstance()->insert('carrier_zone', array('id_carrier' => (int)($carrier->id),
                'id_zone' => (int)($zone['id_zone'])));
                Db::getInstance()->insert('delivery', array('id_carrier' => (int)($carrier->id),
                'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => null,
                'id_zone' => (int)($zone['id_zone']), 'price' => '0'));
                Db::getInstance()->insert('delivery', array('id_carrier' => (int)($carrier->id),
                'id_range_price' => null, 'id_range_weight' => (int)($rangeWeight->id),
                'id_zone' => (int)($zone['id_zone']), 'price' => '0'));
            }

            // Return ID Carrier
            return true;
        }
        return false;
    }

    public function installMails()
    {
        $exclude_langs = array('en');
        $mails_dir = dirname(__FILE__).'/mails/';
        $template_sources = $this->scandir($mails_dir.'en/');

        $languages = $this->getLanguagesInUse();
        foreach ($languages as $lang) {
            if (!in_array($lang['iso_code'], $exclude_langs)) {
                $mail_lang_dir = $mails_dir.$lang['iso_code'].'/';
                if (!file_exists($mail_lang_dir)) {
                    mkdir($mail_lang_dir);
                }

                foreach ($template_sources as $template) {
                    if (!file_exists($mail_lang_dir.$template)) {
                        $this->copy($mails_dir.'en/'.$template, $mail_lang_dir.$template);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCustomShippingRateModule')) == true) {
            $this->postProcess();
        }
        return $this->displayMessages().$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustomShippingRateModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['CUSTOMSHIP_ID_CONTACT'] = Tools::getValue(
            'CUSTOMSHIP_ID_CONTACT',
            Configuration::get('CUSTOMSHIP_ID_CONTACT')
        );
        $helper->fields_value['CUSTOMSHIP_EMAIL_TO'] = Tools::getValue(
            'CUSTOMSHIP_EMAIL_TO',
            Configuration::get('CUSTOMSHIP_EMAIL_TO')
        );
        $helper->fields_value['CUSTOMSHIP_QUOTE_EXPIRES'] = Tools::getValue(
            'CUSTOMSHIP_QUOTE_EXPIRES',
            Configuration::get('CUSTOMSHIP_QUOTE_EXPIRES')
        );
        $helper->fields_value['CUSTOMSHIP_MIN_WEIGHT'] = Tools::getValue(
            'CUSTOMSHIP_MIN_WEIGHT',
            Configuration::get('CUSTOMSHIP_MIN_WEIGHT')
        );
        /* Add with team wassim novatis*/
        $helper->fields_value['CUSTOMSHIP_MIN_HEIGHT'] = Tools::getValue(
            'CUSTOMSHIP_MIN_HEIGHT',
            Configuration::get('CUSTOMSHIP_MIN_HEIGHT')
        );
        $helper->fields_value['CUSTOMSHIP_MIN_WIDTH'] = Tools::getValue(
            'CUSTOMSHIP_MIN_WIDTH',
            Configuration::get('CUSTOMSHIP_MIN_WIDTH')
        );
        /* End*/
        $helper->fields_value['CUSTOMSHIP_MAX_WEIGHT'] = Tools::getValue(
            'CUSTOMSHIP_MAX_WEIGHT',
            Configuration::get('CUSTOMSHIP_MAX_WEIGHT')
        );
        $helper->fields_value['CUSTOMSHIP_MIN_PRICE'] = Tools::getValue(
            'CUSTOMSHIP_MIN_PRICE',
            Configuration::get('CUSTOMSHIP_MIN_PRICE')
        );
        $helper->fields_value['CUSTOMSHIP_MAX_PRICE'] = Tools::getValue(
            'CUSTOMSHIP_MAX_PRICE',
            Configuration::get('CUSTOMSHIP_MAX_PRICE')
        );
        $helper->fields_value['CUSTOMSHIP_AUTO_CLEAN'] = Tools::getValue(
            'CUSTOMSHIP_AUTO_CLEAN',
            Configuration::get('CUSTOMSHIP_AUTO_CLEAN')
        );
        $helper->fields_value['CUSTOMSHIP_ID_TAX_RULES_GROUP'] = Tools::getValue(
            'CUSTOMSHIP_ID_TAX_RULES_GROUP',
            Carrier::getIdTaxRulesGroupByIdCarrier((int)Configuration::get('CUSTOM_SHIPPING_CARRIER_ID'))
        );

        // List of Carriers
        $carriers = Carrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            false,
            null,
            PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
        );
        if ($carriers) {
            $carriers_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIERS'), true);
            if (!$carriers_enabled) {
                $carriers_enabled = array();
            }
            foreach ($carriers as $carrier) {
                if (in_array($carrier['id_carrier'], $carriers_enabled)) {
                    $helper->fields_value['CUSTOMSHIP_CARRIER_LIST_'.$carrier['id_carrier']] = true;
                } else {
                    $helper->fields_value['CUSTOMSHIP_CARRIER_LIST_'.$carrier['id_carrier']] = false;
                }
            }
        }

        // List of Zones
        $zones = $this->getCSRZones();
        if ($zones) {
            $zones_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIER_ZONE_LIST'), true);
            if (!$zones_enabled) {
                $zones_enabled = array();
            }
            foreach ($zones as $zone) {
                if (in_array($zone['id_zone'], $zones_enabled)) {
                    $helper->fields_value['CUSTOMSHIP_CARRIER_ZONE_LIST_'.$zone['id_zone']] = true;
                } else {
                    $helper->fields_value['CUSTOMSHIP_CARRIER_ZONE_LIST_'.$zone['id_zone']] = false;
                }
            }
        }
        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of config form.
     */
    protected function getConfigForm()
    {
        // List of Contacts
        $contacts = array(0 => array(
            'id' => 0,
            'name' => $this->l('Type Email Below')
        ));
        foreach (Contact::getContacts($this->context->language->id) as $contact) {
            $contacts[] = array('id' => $contact['id_contact'], 'name' => $contact['name']." (".$contact['email'].")");
        }

        // List of Carriers
        $carriers = Carrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            false,
            null,
            PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
        );
        $option_carriers = array();
        foreach ($carriers as $carrier) {
            if ($carrier['id_carrier'] != (int)Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')) {
                $option_carriers[] = array(
                    'id_carrier' => $carrier['id_carrier'],
                    'name' => $carrier['name'],
                    'val' => $carrier['id_carrier']
                );
            }
        }

        $defaultCurrency = Currency::getDefaultCurrency();

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Custom Shipping Rate Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Contact to send Email'),
                        'name' => 'CUSTOMSHIP_ID_CONTACT',
                        'hint' => $this->l('Choose a contact to email the shipping quote request email.'),
                        'desc' => $this->l('Choose a contact to email the shipping quote request email.'),
                        'required' => true,
                        'validation' => 'isInt',
                        'options' => array(
                            'query' => $contacts,
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'CUSTOMSHIP_EMAIL_TO',
                        'label' => $this->l('Send Email To'),
                        'hint' => $this->l('Send the quote request to this email address.'),
                        'class'    => 'fixed-width-lg',
                        'desc' => $this->l('Send the quote request to this email address.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Quote Expires After'),
                        'name' => 'CUSTOMSHIP_QUOTE_EXPIRES',
                        'hint' => $this->l('Set a number of days for quote to expire. Set to 0 to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('Set a number of days for quote to expire. Set to 0 to ignore.'),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Carriers to display quote with'),
                        'name'  => 'CUSTOMSHIP_CARRIER_LIST',
                        'values' => array(
                            'query' => $option_carriers,
                            'id' => 'id_carrier',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('Choose which carriers to display the shipping quote request form.
                        Leave all carriers unchecked if you want to display the shipping quote request
                        form when no other carriers are available.'),
                        'hint' => $this->l('Choose which carriers to display the shipping quote request form.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum Package Weight'),
                        'name' => 'CUSTOMSHIP_MIN_WEIGHT',
                        'hint' => $this->l('Set a minimum package weight to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'suffix' => $this->l('kg'),
                        'desc' => $this->l('Set a minimum package weight to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    /* Add with team wassim novatis*/
                    array(
                        'type' => 'text',
                        'label' => $this->l('Largeur minimale du colis'),
                        'name' => 'CUSTOMSHIP_MIN_WIDTH',
                        'hint' => $this->l('Set a minimum package width to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'suffix' => $this->l('mm'),
                        'desc' => $this->l('Set a minimum package width to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Longueur minimale du colis'),
                        'name' => 'CUSTOMSHIP_MIN_HEIGHT',
                        'hint' => $this->l('Set a minimum package height to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'suffix' => $this->l('mm'),
                        'desc' => $this->l('Set a minimum package height to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    /* End*/
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Package Weight'),
                        'name' => 'CUSTOMSHIP_MAX_WEIGHT',
                        'hint' => $this->l('Set a maximum package weight to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'suffix' => $this->l('kg'),
                        'desc' => $this->l('Set a maximum package weight to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum Price'),
                        'name' => 'CUSTOMSHIP_MIN_PRICE',
                        'hint' => $this->l('Set a minimum order price to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'prefix' => $defaultCurrency->sign,
                        'desc' => $this->l('Set a minimum order price to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Price'),
                        'name' => 'CUSTOMSHIP_MAX_PRICE',
                        'hint' => $this->l('Set a maximum order price to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                        'class'    => 'fixed-width-sm',
                        'prefix' => $defaultCurrency->sign,
                        'desc' => $this->l('Set a maximum order price to display the shipping quote
                        request form. Set the value to "0", or leave this field blank to ignore.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Tax'),
                        'name' => 'CUSTOMSHIP_ID_TAX_RULES_GROUP',
                        'options' => array(
                            'query' => TaxRulesGroup::getTaxRulesGroups(true),
                            'id' => 'id_tax_rules_group',
                            'name' => 'name',
                            'default' => array(
                                'label' => $this->l('No tax'),
                                'value' => 0
                            )
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Zones'),
                        'name'  => 'CUSTOMSHIP_CARRIER_ZONE_LIST',
                        'values' => array(
                            'query' => $this->getCSRZones(),
                            'id' => 'id_zone',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('Choose which zones to display the shipping quote request form.'),
                        'hint' => $this->l('Choose which zones to display the shipping quote request form.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Auto Clean'),
                        'name' => 'CUSTOMSHIP_AUTO_CLEAN',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'CUSTOMSHIP_AUTO_CLEAN_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'CUSTOMSHIP_AUTO_CLEAN_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            ),
                        ),
                        'desc' => $this->l('Automatically clean quotation records when customer completes the order.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_errors = array();
        if (Tools::getValue('CUSTOMSHIP_ID_CONTACT') == ''
        && Tools::getValue('CUSTOMSHIP_EMAIL_TO') == '') {
            $form_errors[] = $this->_errors[] = $this->l('Please choose a Contact to send the email');
        }
        if ((Tools::getValue('CUSTOMSHIP_ID_CONTACT') == 0
        && Tools::getValue('CUSTOMSHIP_EMAIL_TO') == '')
        || (Tools::getValue('CUSTOMSHIP_ID_CONTACT') == 0
        && !Validate::isEmail(Tools::getValue('CUSTOMSHIP_EMAIL_TO')))) {
            $form_errors[] = $this->_errors[] = $this->l('Please provide a valid email
            address or choose a Contact to send the email');
        }
        if (count($form_errors) == 0) {
            $form_values = array();
            foreach ($this->getConfigKeys() as $config_key) {
                if (Tools::isSubmit($config_key)) {
                    $form_values[$config_key] = Tools::getValue($config_key, Configuration::get($config_key));
                }
            }

            $csr_carrier = new Carrier(Configuration::get('CUSTOM_SHIPPING_CARRIER_ID'));
            $csr_carrier->setTaxRulesGroup((int)Tools::getValue('CUSTOMSHIP_ID_TAX_RULES_GROUP'));

            $form_values['CUSTOMSHIP_CARRIERS'] = array();
            $carriers = Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            );
            if ($carriers) {
                foreach ($carriers as $carrier) {
                    $value = Tools::getValue('CUSTOMSHIP_CARRIER_LIST_'.$carrier['id_carrier']);
                    if ($value) {
                        $form_values['CUSTOMSHIP_CARRIERS'][] = $value;
                    }
                }
            }
            $form_values['CUSTOMSHIP_CARRIERS'] = json_encode($form_values['CUSTOMSHIP_CARRIERS']);

            $id_carriers = array(1, 2, 3);
            $result_save = Configuration::updateValue('CUSTOMSHIP_CARRIERS', json_encode($id_carriers), true);

            $form_values['CUSTOMSHIP_CARRIER_ZONE_LIST'] = array();
            $zones = $this->getCSRZones();
            if ($zones) {
                foreach ($zones as $zone) {
                    $value = Tools::getValue('CUSTOMSHIP_CARRIER_ZONE_LIST_'.$zone['id_zone']);
                    if ($value) {
                        $form_values['CUSTOMSHIP_CARRIER_ZONE_LIST'][] = $value;
                    }
                }
            }
            $form_values['CUSTOMSHIP_CARRIER_ZONE_LIST'] = json_encode(
                $form_values['CUSTOMSHIP_CARRIER_ZONE_LIST']
            );

            $id_zones = array(1, 2, 3);
            $result_save .= Configuration::updateValue(
                'CUSTOMSHIP_CARRIER_ZONE_LIST',
                json_encode($id_zones),
                true
            );

            foreach ($form_values as $option_key => $form_value) {
                $result_save .= Configuration::updateValue($option_key, $form_value, true);
            }

            if ($result_save == true) {
                $this->_confirmations[] = $this->l('Settings updated');
            }
        }
        return $this->displayMessages();
    }

    public function displayMessages()
    {
        $messages = '';
        foreach ($this->_errors as $error) {
            $messages .= $this->displayError($error);
        }
        foreach ($this->_confirmations as $confirmation) {
            $messages .= $this->displayConfirmation($confirmation);
        }
        //Check for warehouses
        $has_warehouse_carrier_assigned = (bool)Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'warehouse_carrier` LIMIT 1'
        );
        if ($has_warehouse_carrier_assigned) {
            $msg = $this->l('Advanced stock management is enabled.');
            $msg .= ' '.$this->l('You may need to enable this carrier for warehouses.');
            $this->context->smarty->assign('customshipping_warning_msg', $msg);
            $messages .= $this->display(__FILE__, 'views/templates/admin/warning.tpl');
        }
        //Check product carrier relations
        $has_product_carrier_assigned = (bool)Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'product_carrier` LIMIT 1'
        );
        if ($has_product_carrier_assigned) {
            $msg = $this->l('You have product-carrier assignments.');
            $msg .= ' '.$this->l('You may need to enable this carrier for products.');
            $this->context->smarty->assign('customshipping_warning_msg', $msg);
            $messages .= $this->display(__FILE__, 'views/templates/admin/warning.tpl');
        }
        return $messages;
    }

    /**
     * Hooks.
     */
    public function hookActionCarrierUpdate($params)
    {
        $shop_context = Shop::getContext();
        $shop_groups_list = array();
        $shops = Shop::getContextListShopID();

        foreach ($shops as $shop_id) {
            $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);
            if (!in_array($shop_group_id, $shop_groups_list)) {
                $shop_groups_list[] = $shop_group_id;
            }
            $carrier_id = Configuration::get('CUSTOM_SHIPPING_CARRIER_ID', null, $shop_group_id, $shop_id);
            if ((int)($params['id_carrier']) == (int)($carrier_id) ||
                (int)($params['carrier']->id_reference) == (int)($carrier_id)) {
                Configuration::updateValue(
                    'CUSTOM_SHIPPING_CARRIER_ID',
                    (int)($params['carrier']->id),
                    false,
                    $shop_group_id,
                    $shop_id
                );
            }
        }
        switch ($shop_context) {
            case Shop::CONTEXT_ALL:
                $carrier_id = Configuration::get('CUSTOM_SHIPPING_CARRIER_ID');
                if ((int)($params['id_carrier']) == (int)($carrier_id) ||
                    (int)($params['carrier']->id_reference) == (int)($carrier_id)) {
                    Configuration::updateValue(
                        'CUSTOM_SHIPPING_CARRIER_ID',
                        (int)($params['carrier']->id)
                    );
                }
                if (count($shop_groups_list)) {
                    foreach ($shop_groups_list as $shop_group_id) {
                        $carrier_id = Configuration::get('CUSTOM_SHIPPING_CARRIER_ID', null, $shop_group_id);
                        if ((int)($params['id_carrier']) == (int)($carrier_id) ||
                            (int)($params['carrier']->id_reference) == (int)($carrier_id)) {
                            Configuration::updateValue(
                                'CUSTOM_SHIPPING_CARRIER_ID',
                                (int)($params['carrier']->id),
                                false,
                                $shop_group_id
                            );
                        }
                    }
                }
                break;
            case Shop::CONTEXT_GROUP:
                if (count($shop_groups_list)) {
                    foreach ($shop_groups_list as $shop_group_id) {
                        $carrier_id = Configuration::get('CUSTOM_SHIPPING_CARRIER_ID', null, $shop_group_id);
                        if ((int)($params['id_carrier']) == (int)($carrier_id) ||
                            (int)($params['carrier']->id_reference) == (int)($carrier_id)) {
                            Configuration::updateValue(
                                'CUSTOM_SHIPPING_CARRIER_ID',
                                (int)($params['carrier']->id),
                                false,
                                $shop_group_id
                            );
                        }
                    }
                }
                break;
        }
    }

    public function hookActionValidateOrder($params)
    {
        // Remove record when order is placed
        if (Configuration::get('CUSTOMSHIP_AUTO_CLEAN') == 1) {
            if (!Validate::isLoadedObject($params['order'])) {
                die($this->l('Missing parameters'));
            }
            $order = $params['order'];
            $id_cart = $order->id_cart;
            if (isset($id_cart) && $id_cart > 0) {
                $sql = 'DELETE FROM `'._DB_PREFIX_.'customshippingrate` WHERE `id_cart` = '.(int)$id_cart;
                return Db::getInstance()->execute($sql);
            }
        }
    }

    public function hookDisplayHeader($params)
    {
        $this->context->cookie->availableCarriers = $this->checkIfCarrierMatch();
        $id_cart = Context::getContext()->cart->id;
        $id_address_delivery = Context::getContext()->cart->id_address_delivery;
        $id_customer = Context::getContext()->customer->id;
        $customer_email = Context::getContext()->customer->email;
        $customer_secure_key = Context::getContext()->customer->secure_key;
        $id_contact = Configuration::get('CUSTOMSHIP_ID_CONTACT');
        $contact_url = $this->getPathUri().'sendtostore_ajax.php';
        $customshippingrate_token = sha1(_COOKIE_KEY_.'customshippingrate'.$customer_secure_key);
        $this->context->cookie->shippingQuoteValid = $this->checkIfShippingQuoteIsValid($id_cart);
        Media::addJsDef(
            array(
                'customshippingrate_carrier_id' => (int)Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')
            )
        );
        /* Update 090121 */
        Media::addJsDef(
            array(
                'customshippingrate_shipping_price' => $this->getCustomShippingRate($id_cart)
            )
        );
        /* Update 090121 */
        Media::addJsDef(array('contact_url' => $contact_url));
        Media::addJsDef(array('customshippingrate_token' => $customshippingrate_token));
        Media::addJsDef(array('id_contact' => $id_contact));
        Media::addJsDef(array('id_cart' => (int)$id_cart));
        Media::addJsDef(array('id_address_delivery' => (int)$id_address_delivery));
        Media::addJsDef(array('id_customer' => (int)$id_customer));
        Media::addJsDef(array('customer_email' => $customer_email));
        Media::addJsDef(array('customshippingrate_customer_label' => $this->l('Customer ID')));
        Media::addJsDef(
            array(
                'customshippingrate_message' => Tools::safeOutput(
                    $this->l('Please provide a quote for shipping Cart ID')
                )
            )
        );
        Media::addJsDef(
            array(
                'customshippingrate_send_error' => Tools::safeOutput(
                    $this->l('Sorry, an error occurred while sending the message.')
                )
            )
        );
        Media::addJsDef(
            array(
                'customshippingrate_send_success' => Tools::safeOutput(
                    $this->l('Thanks, we\'ll get back to you as soon as possible with a shipping cost for your order.')
                )
            )
        );
        if (isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'product'
        || $this->context->controller->php_self == 'cart'
        || $this->context->controller->php_self == 'order')) {
            if (_PS_VERSION_ > 1.6) {
                $this->context->controller->registerJavascript(
                    'module-customshippingrate-js',
                    'modules/'.$this->name.'/views/js/customshippingrate17.js',
                    array(
                      'position' => 'bottom',
                      'inline' => false,
                      'priority' => 20,
                    )
                );
                $this->context->controller->registerStylesheet(
                    'module-customshippingrate-style',
                    'modules/'.$this->name.'/views/css/customshippingrate17.css',
                    array(
                      'media' => 'all',
                      'priority' => 200,
                    )
                );
            } else {
                $this->context->controller->addCSS($this->_path.'views/css/customshippingrate16.css');
                $this->context->controller->addJS($this->_path.'views/js/customshippingrate16.js');
            }
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $customshippingrate_carrier_id = (int)Configuration::get('CUSTOM_SHIPPING_CARRIER_ID');
        Media::addJsDef(
            array(
                'customshippingrate_carrier_id' => $customshippingrate_carrier_id
            )
        );
        Media::addJsDef(array('customshippingrate_token' => sha1(_COOKIE_KEY_.'customshippingrate')));
        Media::addJsDef(array('customshippingrate_ajax_url' => $this->_path.'ajax.php'));
        Media::addJsDef(array('customshippingrate_name_label' => Tools::safeOutput($this->l('Carrier Name'))));
        Media::addJsDef(array('customshippingrate_delay_label' => Tools::safeOutput($this->l('Transit Time'))));
        Media::addJsDef(
            array(
                'customshippingrate_price_label_tax_excl' => Tools::safeOutput($this->l('Shipping Price (tax excl)'))
            )
        );
        Media::addJsDef(
            array(
                'customshippingrate_price_label_tax_incl' => Tools::safeOutput($this->l('Shipping Price (tax incl)'))
            )
        );
        Media::addJsDef(array('customshippingrate_save_label' => Tools::safeOutput($this->l('Save'))));
        Media::addJsDef(array('customshippingrate_delete_label' => Tools::safeOutput($this->l('Delete'))));
        Media::addJsDef(
            array(
                'customshippingrate_tax_rate' => Tax::getCarrierTaxRate($customshippingrate_carrier_id)
            )
        );
        if (Tools::isSubmit('id_cart')) {
            $id_cart = (int)Tools::getValue('id_cart');
            $carrier_tax_rate = Tax::getCarrierTaxRate((int)Configuration::get('CUSTOM_SHIPPING_CARRIER_ID'));
            Media::addJsDef(
                array(
                    'customshippingrate_name' => $this->getCustomShippingName($id_cart, 1) ?
                    $this->getCustomShippingName($id_cart) : ''
                )
            );
            Media::addJsDef(
                array(
                    'customshippingrate_delay' => $this->getCustomShippingDelay($id_cart, 1) ?
                    $this->getCustomShippingDelay($id_cart) : ''
                )
            );
            Media::addJsDef(
                array(
                    'customshippingrate_price_tax_excl' => $this->getCustomShippingRate($id_cart) ?
                    $this->getCustomShippingRate($id_cart) : '0.00'
                )
            );
            Media::addJsDef(
                array(
                    'customshippingrate_price_tax_incl' => $this->getCustomShippingRate($id_cart) ?
                    $this->getCustomShippingRate($id_cart) * (1 + ($carrier_tax_rate / 100)) : '0.00'
                )
            );
        } else {
            Media::addJsDef(array('customshippingrate_name' => $this->l('Carrier Name')));
            Media::addJsDef(array('customshippingrate_delay' => $this->l('Transit Time')));
            Media::addJsDef(array('customshippingrate_price_tax_excl' => '0.00'));
            Media::addJsDef(array('customshippingrate_price_tax_incl' => '0.00'));
        }
        if (version_compare(_PS_VERSION_, '1.7.7', '>=') === true) {
            Media::addJsDef(array('ps_admin_version' => '1.7'));
        } else {
            Media::addJsDef(array('ps_admin_version' => '1.6'));
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS($this->_path.'views/js/script.js');
    }

    public function hookDisplayBeforeCarrier($params)
    {
        $id_cart = Context::getContext()->cart->id;
        $cart = new Cart($id_cart);

        // overwrite the list of shipping options
        $availableCarriers = array();
        if (_PS_VERSION_ > 1.6) {
            $delivery_option_list = Context::getContext()->cart->getDeliveryOptionList();
            if ($delivery_option_list) {
                foreach ($delivery_option_list as $id_address => $carrier_list_raw) {
                    $appended_carrier_name = $this->getCustomShippingName($id_cart);
                    foreach ($carrier_list_raw as $key => $carrier_list) {
                        foreach ($carrier_list['carrier_list'] as $id_carrier => $carrier) {
                            $CUSTOM_SHIPPING_CARRIER_I = Configuration::get('CUSTOM_SHIPPING_CARRIER_ID');
                            if ($id_carrier != Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')) {
                                $availableCarriers[] = $id_carrier;
                                continue;
                            }
                            $delay = &$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]
                            ['instance']->delay;
                            $delay = array_map(
                                array(
                                    $this,
                                    'getAppendedDelay'
                                ),
                                $delay
                            );
                            $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance']->name =
                            $appended_carrier_name;
                        }
                    }
                }
            }
        } else {
            if (isset($params['delivery_option_list'])) {
                $delivery_option_list = $params['delivery_option_list'];
                foreach ($delivery_option_list as $id_address => $carrier_list_raw) {
                    $appended_carrier_name = $this->getCustomShippingName($id_cart);
                    foreach ($carrier_list_raw as $key => $carrier_list) {
                        foreach ($carrier_list['carrier_list'] as $id_carrier => $carrier) {
                            if ($id_carrier != Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')) {
                                $availableCarriers[] = $id_carrier;
                                continue;
                            }
                            $delay = &$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]
                            ['instance']->delay;
                            $delay = array_map(
                                array(
                                    $this,
                                    'getAppendedDelay'
                                ),
                                $delay
                            );
                            $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance']->name = $appended_carrier_name;
                        }
                    }
                }
                $this->context->smarty->assign('delivery_option_list', $delivery_option_list);
            }
        }

        $carriers_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIERS'), true);
        if (!$carriers_enabled) {
            $carriers_enabled = array();
        }
        $carrierMatch = 0;
        foreach ($availableCarriers as $carrier) {
            if (in_array($carrier, $carriers_enabled)) {
                $carrierMatch = 1;
            }
        }

        $shippingRateApplied = 0;
        if ($this->getCustomShippingRate($id_cart) != false && $this->getCustomShippingRate($id_cart) > 0) {
            $shippingRateApplied = 1;
        }

        $nbProductsSaved = $this->getSavedNbProducts($id_cart);
        $nbProductsCurrent = $cart->getNbProducts($id_cart);
        $totalWeightSaved = intval($this->getTotalWeightSaved($id_cart));
        $totalWeightCurrent = intval($cart->getTotalWeight());
        //$widthCurrent = $cart->getWidthSaved();
        //$heightCurrent = $cart->getHeightSaved();
        $orderTotalSaved = $this->getOrderTotalSaved($id_cart);
        $orderTotalCurrent = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $deliveryAddress = $this->getCustomShippingIdAddress($id_cart);

        if ($nbProductsCurrent > $nbProductsSaved ||
        intval($totalWeightCurrent) > intval($totalWeightSaved) ||
        $orderTotalCurrent > $orderTotalSaved ||
        $deliveryAddress != Context::getContext()->cart->id_address_delivery) {
            $shippingRateApplied = 0;
        }
        $this->context->smarty->assign('customshippingrate_applied', $shippingRateApplied);
        $this->context->smarty->assign('display_with_carriers', $carrierMatch);
        $this->context->smarty->assign('available_carriers_num', count($availableCarriers));

        if (!Context::getContext()->employee || !Context::getContext()->employee->id) {
            if ($shippingRateApplied == 0) {
                if (_PS_VERSION_ > 1.6) {
                    return $this->display(__FILE__, 'views/templates/hook/carriers17.tpl');
                } else {
                    return $this->display(__FILE__, 'views/templates/hook/carriers16.tpl');
                }
            }
        }
    }

    public function getCustomShippingName($id_cart, $flag = 0)
    {
        if ($this->context->cookie->shippingQuoteValid == false) {
            return $this->l('Request shipping cost');
        }
        $quote_expires = Configuration::get('CUSTOMSHIP_QUOTE_EXPIRES');
        if ($quote_expires != '' && $quote_expires > 0) {
            $sql = 'SELECT `carrier_name` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"
            AND DATE(`date_add`) > DATE_SUB(CURDATE(), INTERVAL '.$quote_expires.' DAY)';
        } else {
            $sql = 'SELECT `carrier_name` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"';
        }
        $value = Db::getInstance()->getValue($sql);
        if ($value != '') {
            return $value;
        } elseif ($flag == 1) {
            return $value;
        } else {
            return $this->l('Request shipping cost');
        }
    }

    public function getCustomShippingDelay($id_cart, $flag = 0)
    {
        if ($this->context->cookie->shippingQuoteValid == false) {
            return $this->l('Request shipping cost for your order');
        }
        $quote_expires = Configuration::get('CUSTOMSHIP_QUOTE_EXPIRES');
        if ($quote_expires != '' && $quote_expires > 0) {
            $sql = 'SELECT `carrier_delay` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"
            AND DATE(`date_add`) > DATE_SUB(CURDATE(), INTERVAL '.$quote_expires.' DAY)';
        } else {
            $sql = 'SELECT `carrier_delay` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"';
        }
        $value = Db::getInstance()->getValue($sql);
        if ($value != '') {
            return $value;
        } elseif ($flag == 1) {
            return $value;
        } else {
            return $this->l('Request shipping cost for your order');
        }
    }

    public function getSavedNbProducts($id_cart)
    {
        $sql = 'SELECT `number_products` FROM '._DB_PREFIX_.'customshippingrate WHERE `id_cart` = "'.(int)$id_cart.'"';
        return Db::getInstance()->getValue($sql);
    }

    public function getTotalWeightSaved($id_cart)
    {
        $sql = 'SELECT `total_weight` FROM '._DB_PREFIX_.'customshippingrate WHERE `id_cart` = "'.(int)$id_cart.'"';
        return Db::getInstance()->getValue($sql);
    }

    public function getOrderTotalSaved($id_cart)
    {
        $sql = 'SELECT `order_total` FROM '._DB_PREFIX_.'customshippingrate WHERE `id_cart` = "'.(int)$id_cart.'"';
        return Db::getInstance()->getValue($sql);
    }

    public function getCustomShippingRate($id_cart)
    {
        $quote_expires = Configuration::get('CUSTOMSHIP_QUOTE_EXPIRES');
        if ($quote_expires != '' && $quote_expires > 0) {
            $sql = 'SELECT `shipping_price` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"
            AND DATE(`date_add`) > DATE_SUB(CURDATE(), INTERVAL '.$quote_expires.' DAY)';
        } else {
            $sql = 'SELECT `shipping_price` FROM '._DB_PREFIX_.'customshippingrate
            WHERE `id_cart` = "'.(int)$id_cart.'"';
        }
        $ShippingRate = Db::getInstance()->getValue($sql);
        if ($ShippingRate == '') {
            $ShippingRate = false;
        }
        return $ShippingRate;
    }

    public function getCustomShippingIdAddress($id_cart)
    {
        $sql = 'SELECT `id_address_delivery` FROM '._DB_PREFIX_.'customshippingrate
        WHERE `id_cart` = "'.(int)$id_cart.'"';
        return Db::getInstance()->getValue($sql);
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        return $this->getOrderShippingCostExternal($params);
    }

    public function getOrderShippingCostExternal($params)
    {
        // Prevents infinite loop for carriers match.
        static $preventLoop = null;
        if ( $preventLoop ) {
            // Loop prevented, re-enable this method.
            $preventLoop = false;
            return false;
        }
        $preventLoop = true;
        $id_cart = $params->id;
        $cart = new Cart($id_cart);
        /* Add with team wassim novatis*/
        $products = $cart->getProducts(); 
        $maxWidth = 0;
        $maxHeight = 0;   
        foreach ($products as $product) {           
            $productWidth = (float)$product['width']; 
            $productHeight = (float)$product['height']; 
        
            if ($productWidth > $maxWidth) {
                $maxWidth = $productWidth;
            }
            if ($productHeight > $maxHeight) {
                $maxHeight = $productHeight;
            }  
        }
        /*End*/
        $nbProductsSaved = $this->getSavedNbProducts($id_cart);
        $nbProductsCurrent = $cart->getNbProducts($id_cart);
        $totalWeightSaved = $this->getTotalWeightSaved($id_cart);
        $totalWeightCurrent = $cart->getTotalWeight();
        /* Add with team wassim novatis*/
        $widthCurrent = $maxWidth;
        $heightCurrent = $maxHeight;
        $min_width = (float) Configuration::get('CUSTOMSHIP_MIN_WIDTH');
        $min_height = (float) Configuration::get('CUSTOMSHIP_MIN_HEIGHT');
        /* End*/
        $orderTotalSaved = $this->getOrderTotalSaved($id_cart);
        $orderTotalCurrent = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $deliveryAddress = $this->getCustomShippingIdAddress($id_cart);
        $carriers_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIERS'), true);
        $min_weight = Configuration::get('CUSTOMSHIP_MIN_WEIGHT');
        $max_weight = Configuration::get('CUSTOMSHIP_MAX_WEIGHT');
        $min_price = Configuration::get('CUSTOMSHIP_MIN_PRICE');
        $max_price = Configuration::get('CUSTOMSHIP_MAX_PRICE');
        $address = new Address($cart->id_address_delivery);
        $id_zone = $address->getZoneById($address->id);
        $zones_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIER_ZONE_LIST'), true);
        if (!$zones_enabled) {
            $zones_enabled = array();
        }

        // Don't use cookie since it isn't revalidated yet. Revalidation happens later within hookHeader().
        $carrier_match = explode( '_', $this->checkIfCarrierMatch() );
        //$carrier_match = explode('_', $this->context->cookie->availableCarriers);
        // Carrier data is fetched, set running to false again.
        $preventLoop = false;

        if (!Context::getContext()->employee || !Context::getContext()->employee->id) {
            if ($this->getCustomShippingRate($id_cart) == false ||
            $this->getCustomShippingRate($id_cart) < 0) {
                if (is_array($carriers_enabled)) {
                    if ($carrier_match[0] == 0 && $carrier_match[1] > 0) {
                        return false;
                    }
                }
                if ($min_weight > 0 && $totalWeightCurrent < $min_weight) {
                    return false;
                }
                /* Add with team wassim novatis*/
                if (($min_width > 0 && $widthCurrent <= $min_width) && ($min_height > 0 && $heightCurrent <= $min_height)) {
                    return false;
                }
                /*End*/
                if ($max_weight > 0 && $totalWeightCurrent > $max_weight) {
                    return false;
                }
                if ($min_price > 0 && $orderTotalCurrent < $min_price) {
                    return false;
                }
                if ($max_price > 0 && $orderTotalCurrent > $max_price) {
                    return false;
                }
                if (!in_array($id_zone, $zones_enabled)) {
                    return false;
                }
            }

            if ($nbProductsCurrent > $nbProductsSaved ||
            intval($totalWeightCurrent) > intval($totalWeightSaved) ||
            $orderTotalCurrent > $orderTotalSaved ||
            $deliveryAddress != $cart->id_address_delivery) {
                if ($this->getCustomShippingRate($id_cart) == false ||
                $this->getCustomShippingRate($id_cart) < 0) {
                    return 0;
                }
                if ($this->getCustomShippingRate($id_cart) == true ||
                $this->getCustomShippingRate($id_cart) > 0) {
                    return 0;
                }
            }
        }

        if (in_array($id_zone, $zones_enabled)) {
            $value = $this->getCustomShippingRate($id_cart);
            if (Context::getContext()->employee && Context::getContext()->employee->id) {
                if ($value == '-0.01') {
                    $value = 0.00;
                }
            }
            return $value ? round((float)$value, 2) : 0.00;
        }
        return 0;
    }

    public function getAppendedDelay()
    {
        $id_cart = (int)Context::getContext()->cart->id;
        return $this->getCustomShippingDelay($id_cart);
    }

    public function checkIfCarrierMatch()
    {
        $carriers_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIERS'), true);
        if (!$carriers_enabled) {
            $carriers_enabled = array();
        }
        $carrierMatch = 0;

        $availableCarriers = array();
        if (!empty(Context::getContext()->cart->id)) {
            if (Context::getContext()->cart->getDeliveryOptionList()) {
                $delivery_option_list = Context::getContext()->cart->getDeliveryOptionList();
                foreach ($delivery_option_list as $id_address => $carrier_list_raw) {
                    foreach ($carrier_list_raw as $key => $carrier_list) {
                        foreach ($carrier_list['carrier_list'] as $id_carrier => $carrier) {
                            if ($id_carrier != Configuration::get('CUSTOM_SHIPPING_CARRIER_ID')) {
                                $availableCarriers[] = $id_carrier;
                                continue;
                            }
                        }
                    }
                }
            }
        }

        foreach ($availableCarriers as $carrier) {
            if (in_array($carrier, $carriers_enabled)) {
                $carrierMatch = 1;
            }
        }

        return $carrierMatch.'_'.count($availableCarriers);
    }

    public function checkIfShippingQuoteIsValid($id_cart)
    {
        $cart = new Cart($id_cart);
        $nbProductsSaved = $this->getSavedNbProducts($id_cart);
        $nbProductsCurrent = $cart->getNbProducts($id_cart);
        $totalWeightSaved = $this->getTotalWeightSaved($id_cart);
        $totalWeightCurrent = $cart->getTotalWeight();
        $orderTotalSaved = $this->getOrderTotalSaved($id_cart);
        $orderTotalCurrent = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $deliveryAddress = $this->getCustomShippingIdAddress($id_cart);
        $carriers_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIERS'), true);
        $carrier_match = explode('_', $this->context->cookie->availableCarriers);
        $min_weight = Configuration::get('CUSTOMSHIP_MIN_WEIGHT');
        $max_weight = Configuration::get('CUSTOMSHIP_MAX_WEIGHT');
        $min_price = Configuration::get('CUSTOMSHIP_MIN_PRICE');
        $max_price = Configuration::get('CUSTOMSHIP_MAX_PRICE');
        $address = new Address($cart->id_address_delivery);
        $id_zone = $address->getZoneById($address->id);
        $zones_enabled = json_decode(Configuration::get('CUSTOMSHIP_CARRIER_ZONE_LIST'), true);
        if (!$zones_enabled) {
            $zones_enabled = array();
        }

        if (!Context::getContext()->employee || !Context::getContext()->employee->id) {
            if ($this->getCustomShippingRate($id_cart) == false ||
            $this->getCustomShippingRate($id_cart) < 0) {
                if (is_array($carriers_enabled)) {
                    if ($carrier_match[0] == 0 && $carrier_match[1] > 0) {
                        return false;
                    }
                }
                if ($min_weight > 0 && $totalWeightCurrent < $min_weight) {
                    return false;
                }
                if ($max_weight > 0 && $totalWeightCurrent > $max_weight) {
                    return false;
                }
                if ($min_price > 0 && $orderTotalCurrent < $min_price) {
                    return false;
                }
                if ($max_price > 0 && $orderTotalCurrent > $max_price) {
                    return false;
                }
                if (!in_array($id_zone, $zones_enabled)) {
                    return false;
                }
            }

            if ($nbProductsCurrent > $nbProductsSaved ||
            intval($totalWeightCurrent) > intval($totalWeightSaved) ||
            $orderTotalCurrent > $orderTotalSaved ||
            $deliveryAddress != $cart->id_address_delivery) {
                if ($this->getCustomShippingRate($id_cart) == false ||
                $this->getCustomShippingRate($id_cart) < 0) {
                    return false;
                }
                if ($this->getCustomShippingRate($id_cart) == true ||
                $this->getCustomShippingRate($id_cart) > 0) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getCSRCarriers($with_referenced_carriers = false, $id_shop = null)
    {
        $ids_carrier = explode(',', Configuration::get('CUSTOMSHIP_CARRIERS', null, null, $id_shop));
        if ($with_referenced_carriers === false) {
            return $ids_carrier;
        } else {
            $ids_ref_carriers = array();
            foreach ($ids_carrier as $id_carrier) {
                $carrier = new Carrier((int)$id_carrier);
                $ids_referenced_carrier = Db::getInstance()->executeS(
                    'SELECT `id_carrier` FROM `'._DB_PREFIX_.'carrier`
                    WHERE id_reference = '.(int)$carrier->id_reference.' ORDER BY id_carrier'
                );
                foreach ($ids_referenced_carrier as $id_referenced_carrier) {
                    $ids_ref_carriers[] = (int)$id_referenced_carrier['id_carrier'];
                }
            }
            return $ids_ref_carriers;
        }
    }

    public function getCSRZones()
    {
        $zones = Zone::getZones(true);
        foreach ($zones as &$zone) {
            $zone['val'] = $zone['id_zone'];
        }
        return $zones;
    }

    public function getLanguagesInUse()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = array();
        foreach (Language::getLanguages(false) as $lang) {
            $languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        return $languages;
    }

    public function scandir($path, $ext = '', $dir = '', $recursive = false)
    {
        $path = rtrim(rtrim($path, '\\'), '/').'/';
        $real_path = rtrim(rtrim($path.$dir, '\\'), '/').'/';
        $files = scandir($real_path);
        if (!$files) {
            return array();
        }

        $filtered_files = array();

        $real_ext = false;
        if (!empty($ext)) {
            $real_ext = '.'.$ext;
        }
        $real_ext_length = Tools::strlen($real_ext);

        $subdir = ($dir) ? $dir.'/' : '';
        foreach ($files as $file) {
            if (!$real_ext || (strpos($file, $real_ext) &&
                    strpos($file, $real_ext) == (Tools::strlen($file) - $real_ext_length))) {
                if (!in_array($file, array('.', '..'))) {
                    $filtered_files[] = $subdir.$file;
                }
            }

            if ($recursive && $file[0] != '.' && is_dir($real_path.$file)) {
                foreach (Tools::scandir($path, $ext, $subdir.$file, $recursive) as $subfile) {
                    $filtered_files[] = $subfile;
                }
            }
        }
        return $filtered_files;
    }

    public function copy($source, $destination, $stream_context = null)
    {
        if (is_null($stream_context) && !preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }
        return @file_put_contents($destination, Tools::file_get_contents($source, false, $stream_context));
    }

    public function getDefaultConfig()
    {
        return $this->default_config;
    }

    public function getConfigKeys()
    {
        return array_keys($this->getDefaultConfig());
    }
}
