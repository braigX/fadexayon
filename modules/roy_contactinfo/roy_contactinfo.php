<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Roy_Contactinfo extends Module implements WidgetInterface
{
    private $templates = array (
        'callus' => 'roy_nav.tpl',
        'contactus' => 'roy_nav2.tpl',
        'rich' => 'roy_contactinfo-rich.tpl',
        'default' => 'roy_contactinfo.tpl',
    );

    public function __construct()
    {
        $this->name = 'roy_contactinfo';
        $this->author = 'RoyThemes';
        $this->version = '3.0.1';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->getTranslator()->trans('Roy Contact information', array(), 'Modules.Roycontactinfo.Admin');
        $this->description = $this->getTranslator()->trans('Allows you to display additional information about your store\'s customer service.', array(), 'Modules.Roycontactinfo.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.2.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook([
                'displayNav', // Standard hook
                'displayNav1',
                'displayNav2',
                'displayFooter',
                'actionAdminStoresControllerUpdate_optionsAfter',
            ])
        ;
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if ($hookName == 'displayNav1') {
            $template_file = $this->templates['callus'];
        } elseif ($hookName == 'displayNav2') {
            $template_file = $this->templates['contactus'];
        } elseif ($hookName == 'displayLeftColumn') {
            $template_file = $this->templates['rich'];
        } else {
            $template_file = $this->templates['default'];
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/'.$template_file);
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $address = $this->context->shop->getAddress();

        $contact_infos = [
            'company' => Configuration::get('PS_SHOP_NAME'),
            'address' => [
                'formatted' => AddressFormat::generateAddress($address, array(), '<br />'),
                'address1' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => (new State($address->id_state))->name,
                'country' => (new Country($address->id_country))->name[$this->context->language->id],
            ],
            'phone' => Configuration::get('PS_SHOP_PHONE'),
            'fax' => Configuration::get('PS_SHOP_FAX'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
        ];

        return [
            'contact_infos' => $contact_infos,
        ];
    }

    public function hookActionAdminStoresControllerUpdate_optionsAfter()
    {
        foreach ($this->templates as $template) {
            $this->_clearCache($template);
        }

        return true;
    }
}
