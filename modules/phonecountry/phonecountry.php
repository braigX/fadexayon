<?php
/**
* 2007-2022 PrestaShop
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
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Phonecountry extends Module
{
    protected $config_form = false;
    public $activeTab;
    public $ec_token;
    public $protocol;
    public $author_address;
    public $list_ec_phonecountry;

    public function __construct()
    {
        $this->name = 'phonecountry';
        $this->tab = 'administration';
        $this->version = '2.0.2';
        $this->author = 'Ether Creation';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->activeTab = false;
        $this->displayName = $this->l('Phone Country');
        $this->description = $this->l('A module to automatically change the phone number to the international version');
        $this->ec_token = Configuration::getGlobalValue('PHONECOUNTRY_TOKEN');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->protocol = (((Configuration::get('PS_SSL_ENABLED') == 1) && (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') == 1)) ? 'https://' : 'http://' );
        $this->module_key = '43f20a905110b9972583183a5dfa3406';
        $this->author_address = '0x5B0Db755Ab94Da39bF52aEfF97bF830fD6ABc7a8';
        $this->list_ec_phonecountry = array(
            'id_ec_phonecountry' => array(
                'name' => 'id_ec_phonecountry',
                'type' => 'text',
            ),
            'iso_code' => array(
                'name' => 'c.iso_code',
                'type' => 'text',
            ),
            'call_prefix' => array(
                'name' => 'call_prefix',
                'type' => 'text',
            ),
            'name' => array(
                'name' => 'name',
                'type' => 'text',
            ),
            'fixe' => array(
                'name' => 'fixe',
                'type' => 'text',
            ),
            'mobile' => array(
                'name' => 'mobile',
                'type' => 'text',
            ),
            'active' => array(
                'name' => 'active',
                'type' => 'text',
            ),
        );
    }

    
    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');
        Configuration::updateGlobalValue('PHONECOUNTRY_TOKEN', md5(time()._COOKIE_KEY_));
        
        return parent::install() && $this->registerHook('actionAdminControllerSetMedia') && $this->registerHook('Header');
    }

    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'ec_phonecountry`');
        return parent::uninstall();
    }

    public function getContent()
    {
        $html = '';
        $mess = '';
        $redirect = false;
        $values = Tools::getAllValues();
        if (Tools::isSubmit('updateec_phonecountry')) {
            $id = Tools::getValue('id_ec_phonecountry');
            return $this->getEditForm($id);
        }
        if (Tools::isSubmit('submitEditPhoneCountry')) {
            $id = Tools::getValue('id_ec_phonecountry');
            $fixe = Tools::getValue('fixe');
            $fixe_values = explode('|', $fixe);
            foreach ($fixe_values as $val) {
                if (!Validate::isInt($val) && Tools::strlen($val) > 0) {
                    return $this->displayError($this->l('Mobile and fixe rule must ')).$this->getEditForm($id);
                }
            }
            $mobile = Tools::getValue('mobile');
            $mobile_values = explode('|', $mobile);
            foreach ($mobile_values as $val) {
                if (!Validate::isInt($val) && Tools::strlen($val) > 0) {
                    return $this->displayError($this->l('Mobile and fixe rule must ')).$this->getEditForm($id);
                }
            }
            
            $active = Tools::getValue('active');
            $country = new Country($id);

            if ($country->active != $active) {
                $country->active = (int)$active;
                $country->save();
            }
            Db::getinstance()->update(
                'ec_phonecountry',
                array(
                    'fixe' => pSQL($fixe),
                    'mobile' => pSQL($mobile)
                ),
                'id_ec_phonecountry = '.(int)$id
            );
            $mess = '&maj=1&ec_tab=ec_phonecountry';
            $redirect = true;
        }
      
        if ($redirect) {
            $link = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.$mess;
            $admin_dir = explode('/', _PS_ADMIN_DIR_);
            $admin_dir = $admin_dir[count($admin_dir)-1];
            if (strpos($link, $admin_dir) === false) {
                $link = Tools::getHttpHost(true).__PS_BASE_URI__.$admin_dir.'/'.$link;
            }
            Tools::redirect($link);
        }
        if (Tools::getValue('maj') == 1) {
            $html .= $this->displayConfirmation($this->l('Successful update'));
        }
        $this->activeTab = Tools::getValue('ec_tab');
        
        $list_s = array('ec_phonecountry', 'phonecountry_log');
        foreach ($list_s as $name) {
            $search = Tools::getValue('submitFilter'.$name);
            if (isset($values['submitReset'.$name]) || $search || isset($values[$name.'_pagination']) || isset($values[$name.'Orderby'])) {
                $this->activeTab = $name;
            }
        }
        return $html.$this->showMenu().$this->showHelp().$this->renderList();
    }
    
    
    protected function renderList()
    {
        $name = 'ec_phonecountry';
        $this->fields_list = array();
        $this->fields_list['id_ec_phonecountry'] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => true,
            'class' => 'fixed-width-xs',
            'orderby' => true,
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Country name'),
            'type' => 'text',
            'search' => true,
            'orderby' => true,
        );
        $this->fields_list['iso_code'] = array(
                'title' => $this->l('Iso code'),
                'type' => 'text',
                'search' => true,
                'orderby' => false,
                'class' => 'fixed-width-xs',
        );
        $this->fields_list['call_prefix'] = array(
            'title' => $this->l('Prefix'),
            'type' => 'text',
            'search' => true,
            'orderby' => false,
        );
        $this->fields_list['fixe'] = array(
            'title' => $this->l('Fixe rule'),
            'type' => 'text',
            'search' => true,
            'orderby' => false,
        );
        $this->fields_list['mobile'] = array(
            'title' => $this->l('Mobile rule'),
            'type' => 'text',
            'search' => true,
            'orderby' => false,
        );
        $this->fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'bool',
            'search' => true,
            'orderby' => false,
            'class' => 'fixed-width-xs center',
            'callback_object' => Module::getInstanceByName($this->name),
            'callback' => 'showIcon'
        );
        
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->_orderBy = 'id_ec_phonecountry';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_ec_phonecountry';
        $helper->actions = array('edit');
        $helper->show_toolbar = true;
        $helper->title = $this->l('List of countries');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContent($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $this->fields_list);
    }

    public function showIcon($row)
	{
		if ($row == 1) {
			return '<i class="icon-check" style="color:#72C279;"></i>';
		} else {
			return '<i class="icon-remove" style="color:#E08F95;"></i>';
		}
	}
    
    public function getListContent($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id_ec_phonecountry, cl.name, c.iso_code, c.call_prefix, c.active, ep.fixe, ep.mobile FROM '._DB_PREFIX_.'ec_phonecountry ep
            LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country = ep.id_ec_phonecountry)
            LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = c.id_country)
            LEFT JOIN '._DB_PREFIX_.'country_shop cs ON (cs.id_country = c.id_country)
            WHERE cl.id_lang = '.(int)$id_lang.' AND cs.id_shop = '.(int)$id_shop.'
        ';
        $values = Tools::getAllValues();
        $nameFilter = $name.'Filter_';
        if (isset($values['submitReset'.$name])) {
            foreach ($this->list_ec_phonecountry as $name => $info) {
                $_POST[$nameFilter.$name] = '';
            }
        }
        $search = Tools::getValue('submitFilter'.$name);
        if ($search) {
 
            foreach ($this->list_ec_phonecountry as $name => $info) {
                $val = Tools::getValue($nameFilter.$name);
                if (Tools::strlen($val) > 0) {
                    if ($info['type'] == 'date') {
                        if (isset($val[0])) {
                            $req.= ' AND '.$info['name'].' >= "'.pSQL($val[0]).'"';
                        }
                        if (isset($val[1])) {
                            $req.= ' AND '.$info['name'].' <= "'.pSQL($val[1]).'"';
                        }
                    } else {
                        $req.= ' AND '.$info['name'].' like "%'.pSQL($val).'%"';
                    }
                }
            }
        }

        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        } else {
            $req .= ' ORDER BY id_ec_phonecountry ASC';
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        $res = Db::getInstance()->ExecuteS($req);
        return array('res' => $res, 'count' => $count);
    }


  
    protected function getEditForm($id)
    {
        
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Edit phone rule'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_ec_phonecountry',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Country name'),
                        'name' => 'name',
                        'disabled' => true,
                        'col' => 2
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Iso code'),
                        'name' => 'iso_code',
                        'disabled' => true,
                        'col' => 1
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prefix'),
                        'name' => 'call_prefix',
                        'disabled' => true,
                        'col' => 1
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Fixe rule'),
                        'name' => 'fixe',
                        'desc' => $this->l('Number of digits allowed, if multiple values, separate them by "|"'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mobile Rule'),
                        'name' => 'mobile',
                        'desc' => $this->l('Number of digits allowed, if multiple values, separate them by "|"'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'ec_phonecountry';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEditPhoneCountry';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', true)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        ;
        $values = Db::getInstance()->getRow('SELECT id_ec_phonecountry, cl.name, c.iso_code, c.call_prefix, c.active, ep.fixe, ep.mobile FROM '._DB_PREFIX_.'ec_phonecountry ep
        LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country = ep.id_ec_phonecountry)
        LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = c.id_country)
        LEFT JOIN '._DB_PREFIX_.'country_shop cs ON (cs.id_country = c.id_country)
        WHERE cl.id_lang = '.(int)$id_lang.' AND cs.id_shop = '.(int)$id_shop.' AND id_ec_phonecountry = '.(int)$id);

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($form));
    }
  

  

    public function showMenu()
    {
        $this->smarty->assign(array(
            'active' => $this->activeTab,
            'ec_id_shop' => (int)$this->context->shop->id,
            'ec_img_dir' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/views/img',
        ));
        return $this->display(__FILE__, 'views/templates/admin/menu.tpl');
    }

    public function showHelp()
    {
        $iso_code = $this->context->language->iso_code;
        $tab_link_super_hero = array(
            'fr' => 'https://addons.prestashop.com/fr/content/40-echelle-d-expertise-prestashop-bien-choisir-son-developpeur',
            'en' => 'https://addons.prestashop.com/en/content/40-prestashops-expertise-ranking-choose-the-right-developer',
            'es' => 'https://addons.prestashop.com/es/content/40-ranking-de-experiencia-prestashop-para-escoger-al-desarrollador-adecuado',
            'it' => 'https://addons.prestashop.com/it/content/40-livello-di-esperienza-prestashop-un-supporto-per-aiutarti-a-scegliere-lo-sviluppatore-giusto',
        );
        if (!isset($tab_link_super_hero[$iso_code])) {
            $link_super_hero = $tab_link_super_hero['en'];
        } else {
            $link_super_hero = $tab_link_super_hero[$iso_code];
        }
        $this->smarty->assign(array(
            'link_module' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/',
            'm_img_dir' => Tools::getHttpHost(true).__PS_BASE_URI__.'/modules/'.$this->name.'/views/img/',
            'link_super_hero' => $link_super_hero,
            'link_ec_seo' => $iso_code == 'fr'?'https://addons.prestashop.com/fr/seo-referencement-naturel/49487-smartkeyword-seo-referencement-google-5.html':'https://addons.prestashop.com/en/seo-natural-search-engine-optimization/49487-smartkeyword-seo-search-engine-optimization-5.html',
        ));
        return $this->display(__FILE__, 'views/templates/admin/help.tpl');
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            Media::addJsDef(
                array(
                    'phonecountry_ajax' => $this->context->link->getModuleLink('phonecountry', 'ajax', array('ec_token' => $this->ec_token)),
                )
            );
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
            $this->context->controller->addJS($this->_path.'views/js/menu.js');
            $this->context->controller->addCSS($this->_path.'views/css/menu.css');
          

        }
    }

    public function hookHeader()
    {
        $postGetData = Tools::getAllValues();
        if ($postGetData['controller'] == 'order' /* && (isset($postGetData['newAddress']) || isset($postGetData['editAddress']))) || $postGetData['controller'] == 'address' */ ||  $postGetData['controller'] == 'address') {
            $id_shop = (int)$this->context->shop->id;
            $tab_phone_country = array();
            $infos = Db::getInstance()->executes('SELECT c.id_country, c.call_prefix, c.active, ep.fixe, ep.mobile FROM '._DB_PREFIX_.'ec_phonecountry ep
            LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country = ep.id_ec_phonecountry)
            LEFT JOIN '._DB_PREFIX_.'country_shop cs ON (cs.id_country = c.id_country)
            WHERE c.active = 1 AND cs.id_shop = '.(int)$id_shop);
            foreach ($infos as $info) {
                $tab_phone_country[$info['id_country']]= $info;
            }
            Media::addJsDef(array('tab_phone_country' => json_encode($tab_phone_country)));
            $this->context->controller->addJS($this->_path . 'views/js/front.js');
        }
    }
}
