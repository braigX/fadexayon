<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccountPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestationOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrierOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';

class AdminGeodisConfigurationBackController extends GeodisControllerAdminAbstractMenu
{
    protected $prestationCollection = null;
    protected $accountPrestationCollection = null;
    protected $accountCollection = null;
    protected $prestationOptionCollection = null;
    protected $carrierCollection = null;
    protected $groupCarrierCollection = null;
    protected $carrierOptionCollection = null;
    protected $optionCollection = null;
    protected $prestashopCarrierCollection = null;
    protected $jsonData = array();

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        $this->page_header_toolbar_title =
        GeodisServiceTranslation::get('*.*.menu.back')->setDefault('My back configuration');
        if (Tools::getValue('submit')) {
            $this->processForm();
        }
    }

    public function renderList()
    {
        if (Tools::getIsset('data')) {
            $this->jsonData = json_decode(Tools::getValue('data'), true);
        }

        $this->assignTemplateVars();
        $action = Tools::getValue('action', 'index');


        switch ($action) {
            case 'ajax-template':
                $this->ajaxTemplateAction();
                break;
            case 'ajax-save':
                $this->ajaxSaveAction();
                break;
            default:
                $this->indexAction();
        }

        return parent::renderView();
    }

    protected function indexAction()
    {
        $this->base_tpl_view = 'main.tpl';

        $this->tpl_view_vars['form'] = $this->getForm();
    }

    protected function ajaxTemplateAction()
    {
        $this->base_tpl_view = 'ajax.tpl';
    }

    /**
     * Check if a carrier is not used twice
     */
    protected function validatePrestaShopCarrierUniq()
    {
        $groupCarrierCollection = $this->getGroupCarrierCollection();
        $prestaShopCarrierReference = array();

        foreach ($groupCarrierCollection as $groupCarrier) {
            if ($groupCarrier['id_reference_carrier'] == 'new' || !$groupCarrier['id_reference_carrier']) {
                continue;
            }

            if (in_array($groupCarrier['id_reference_carrier'], $prestaShopCarrierReference)) {
                $groupCarrierObject = Carrier::getCarrierByReference($groupCarrier['id_reference_carrier']);
                throw new Exception(
                    (string) GeodisServiceTranslation::get(
                        'Admin.ConfigurationBack.ajaxSave.error.nonUniqCarrierReference.%s'
                    )->addVar($groupCarrierObject->name)
                );
            }

            $prestaShopCarrierReference[] = $groupCarrier['id_reference_carrier'];
        }
    }

    /**
     * Check if the prices are valid
     */
    protected function validatePrice()
    {
        $carrierCollection = $this->getCarrierCollection();
        $carrierOptionCollection = $this->getCarrierOptionCollection();

        foreach ($carrierCollection as $carrier) {
            if ($carrier['price'] != (string) (float) $carrier['price'] || $carrier['price'] < 0) {
                throw new Exception(
                    (string) GeodisServiceTranslation::get('Admin.ConfigurationBack.ajaxSave.error.price.%s')
                        ->addVar($carrier['price'])
                );
            }
        }

        foreach ($carrierOptionCollection as $carrierOption) {
            if ($carrierOption['price_impact'] != (string) (float) $carrierOption['price_impact']
                || $carrierOption['price_impact'] < 0
            ) {
                throw new Exception(
                    (string) GeodisServiceTranslation::get('Admin.ConfigurationBack.ajaxSave.error.priceImpact.%s')
                        ->addVar($carrierOption['price_impact'])
                );
            }
        }
    }

    protected function ajaxSaveAction()
    {
        $result = array();

        $result['status'] = 'success';
        $result['data'] = $this->getJsonData();
        $result['noticeMessageList'] = array();


        // Process remove carrier option
        try {
            $this->validatePrestaShopCarrierUniq();
            $this->validatePrice();

            foreach ($result['data']['carrierOptionCollection'] as $key => $carrierOption) {
                if (isset($carrierOption['removed']) && !empty($carrierOption['id'])) {
                    $carrierOptionObject = new GeodisCarrierOption($carrierOption['id']);
                    $carrierOptionObject->delete();
                    unset($result['data']['carrierOptionCollection'][$key]);
                }
            }

            foreach ($result['data']['groupCarrierCollection'] as $key => $group) {
                $groupCarrierObject = new GeodisGroupCarrier($group['id']);

                $groupCarrierObject->preparation_delay = $group['preparation_delay'];
                $groupCarrierObject->active = $group['active'];
                $groupCarrierObject->save();

                $result['data']['groupCarrierCollection'][$key]['id'] = $groupCarrierObject->id;
            }

            foreach ($result['data']['carrierCollection'] as $key => $carrier) {
                $carrierObject = new GeodisCarrier($carrier['id']);

                if (isset($carrier['removed']) || empty($carrier['id_prestation'])) {
                    if (!empty($carrier['id'])) {
                        $carrierObject->deleted = 1;
                        $carrierObject->save();
                        unset($result['data']['carrierCollection'][$key]);
                    }
                } else {
                    $carrierObject->active = (bool) $carrier['active'];
                    $carrierObject->id_account = (int) $carrier['id_account'];
                    $carrierObject->id_prestation = (int) $carrier['id_prestation'];
                    $carrierObject->price = (float) $carrier['price'];
                    $carrierObject->free_shipping_from = (float) $carrier['free_shipping_from'];
                    $carrierObject->additional_shipping_cost = (bool) $carrier['additional_shipping_cost'];
                    if (isset($carrier['enable_price_fixed'])) {
                        $carrierObject->enable_price_fixed = (bool) $carrier['enable_price_fixed'];
                    }
                    if (isset($carrier['enable_price_according'])) {
                        $carrierObject->enable_price_according = (bool)$carrier['enable_price_according'];
                    }
                    if (isset($carrier['enable_free_shipping'])) {
                        $carrierObject->enable_free_shipping = (bool)$carrier['enable_free_shipping'];
                    }
                    if (isset($carrier['id_group_carrier'])) {
                        $carrierObject->id_group_carrier = $carrier['id_group_carrier'];
                    } else {
                        $carrierObject->id_group_carrier =
                        $result['data']['groupCarrierCollection'][$carrier['key_group_carrier']]['id'];
                    }

                    $carrierObject->name = empty($carrier['name']) ? 'default' : $carrier['name'];
                    $carrierObject->description = empty($carrier['description']) ? 'default' : $carrier['description'];
                    $carrierObject->save();

                    $result['data']['carrierCollection'][$key]['id'] = $carrierObject->id;
                }
            }

            foreach ($result['data']['carrierOptionCollection'] as $key => $carrierOption) {
                if (!isset($carrierOption['id'])) {
                    $carrierOption['id'] = null;
                }

                $carrierOptionObject = new GeodisCarrierOption($carrierOption['id']);
                $carrierOptionObject->active = (bool) $carrierOption['active'];
                $carrierOptionObject->price_impact = (float) $carrierOption['price_impact'];
                $carrierOptionObject->id_option = (int) $carrierOption['id_option'];

                if ($carrierOption['id_carrier']) {
                    $carrierOptionObject->id_carrier = (int) $carrierOption['id_carrier'];
                } else {
                    $carrierOptionObject->id_carrier = (int) $result['data']['carrierCollection'][
                        (int) $carrierOption['key_carrier']
                    ]['id'];
                }

                $carrierOptionObject->save();

                $result['data']['carrierOptionCollection'][$key]['id'] = $carrierOptionObject->id;
                $result['data']['carrierOptionCollection'][$key]['id_carrier'] = $carrierOptionObject->id_carrier;
            }

            $result['message'] = (string) GeodisServiceTranslation::get(
                'Admin.ConfigurationBack.AjaxSave.success'
            );
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }

        echo $this->jsonStrClean($result);
        die();
    }

    protected function jsonStrClean($json)
    {
        return str_replace("\u0022", "", json_encode($json, JSON_HEX_QUOT));
    }

    protected function assignTemplateVars()
    {
        if (!isset($this->tpl_view_vars)) {
            $this->tpl_view_vars = array();
        }

        $this->tpl_view_vars['prestationCollection'] = $this->getPrestationCollection();
        $this->tpl_view_vars['prestashopCarrierCollection'] = $this->serializeCollection(
            $this->getPrestaShopCarrierCollection()
        );
        $this->tpl_view_vars['accountPrestationCollection'] = $this->getAccountPrestationCollection();
        $this->tpl_view_vars['accountCollection'] = $this->getAccountCollection();
        $this->tpl_view_vars['prestationOptionCollection'] = $this->getPrestationOptionCollection();
        $this->tpl_view_vars['carrierCollection'] = $this->getCarrierCollection();
        $this->tpl_view_vars['groupCarrierCollection'] = $this->getGroupCarrierCollection();
        $this->tpl_view_vars['carrierOptionCollection'] = $this->getCarrierOptionCollection();

        $this->tpl_view_vars['optionCollection'] = $this->getOptionCollection();
        $this->tpl_view_vars['jsonData'] = $this->jsonStrClean($this->getJsonData());
        $this->tpl_view_vars['ajaxTemplateLink'] = Context::getContext()->link->getAdminLink(
            GEODIS_ADMIN_PREFIX.'ConfigurationBack',
            true,
            array(),
            array('action' => 'ajax-template')
        );
        $this->tpl_view_vars['ajaxSaveLink'] = Context::getContext()->link->getAdminLink(
            GEODIS_ADMIN_PREFIX.'ConfigurationBack',
            true,
            array(),
            array('action' => 'ajax-save')
        );
    }

    protected function getJsonData()
    {
        return array(
            'prestationCollection' => $this->serializeCollection($this->getPrestationCollection()),
            'accountPrestationCollection' => $this->serializeCollection($this->getAccountPrestationCollection()),
            'accountCollection' => $this->serializeCollection(
                $this->getAccountCollection(),
                array('name' => 'getName')
            ),
            'prestationOptionCollection' => $this->serializeCollection($this->getPrestationOptionCollection()),
            'carrierCollection' => $this->serializeCollection($this->getCarrierCollection()),
            'groupCarrierCollection' => $this->serializeCollection(
                $this->getGroupCarrierCollection(),
                array('name' => 'getDefaultName')
            ),
            'carrierOptionCollection' => $this->serializeCollection($this->getCarrierOptionCollection()),
            'optionCollection' => $this->addNameAndDescriptionToOptionList(
                $this->serializeCollection($this->getOptionCollection())
            ),
            'prestashopCarrierCollection' => $this->serializeCollection($this->getPrestaShopCarrierCollection()),
        );
    }

    protected function serializeCollection($collection, $additionalFields = array())
    {
        if (!count($collection)) {
            return array();
        }

        if (is_array($collection)) {
            return $collection;
        }

        // Get the first item to get the classname
        $className = get_class($collection->getFirst());

        $definition = $className::$definition;

        $data = array();

        foreach ($collection as $item) {
            $row = array(
                'id' => $item->id,
            );

            foreach (array_keys($definition['fields']) as $attribute) {
                if (in_array($attribute, array('date_add', 'date_upd'))) {
                    continue;
                }

                $row[$attribute] = $item->$attribute;
            }

            foreach ($additionalFields as $field => $method) {
                $row[$field] = $item->$method();
            }

            $data[] = $row;
        }

        return $data;
    }

    protected function addNameAndDescriptionToOptionList($optionList)
    {
        // Specificity for the options, add name and description
        foreach ($optionList as &$option) {
            $option['name'] = (string) GeodisServiceTranslation::get(
                'Admin.ConfigurationBack.option.name.'.$option['code']
            );
            $option['description'] = (string) GeodisServiceTranslation::get(
                'Admin.ConfigurationBack.option.description.'.$option['code']
            );
        }

        return $optionList;
    }

    protected function jsonDataIsset($attribute)
    {
        return isset($this->jsonData[$attribute]);
    }

    protected function getJsonPostData($attribute)
    {
        return $this->jsonData[$attribute];
    }

    protected function getPrestationCollection()
    {
        if ($this->jsonDataIsset('prestationCollection')) {
            return $this->getJsonPostData('prestationCollection');
        }

        if (is_null($this->prestationCollection)) {
            $this->prestationCollection = GeodisPrestation::getCollection();

            $this->prestationCollection->where('type_service', '=', 'PREPA.EXPE');
        }

        return $this->prestationCollection;
    }

    protected function getAccountPrestationCollection()
    {
        if ($this->jsonDataIsset('accountPrestationCollection')) {
            return $this->getJsonPostData('accountPrestationCollection');
        }

        if (is_null($this->accountPrestationCollection)) {
            $this->accountPrestationCollection = GeodisAccountPrestation::getCollection();
        }

        return $this->accountPrestationCollection;
    }

    protected function getAccountCollection()
    {
        if ($this->jsonDataIsset('accountCollection')) {
            return $this->getJsonPostData('accountCollection');
        }

        if (is_null($this->accountCollection)) {
            $this->accountCollection = GeodisAccount::getCollection();
        }

        return $this->accountCollection;
    }

    protected function getPrestationOptionCollection()
    {
        if ($this->jsonDataIsset('prestationOptionCollection')) {
            return $this->getJsonPostData('prestationOptionCollection');
        }

        if (is_null($this->prestationOptionCollection)) {
            $this->prestationOptionCollection = GeodisPrestationOption::getCollection();
        }

        return $this->prestationOptionCollection;
    }

    protected function getOptionCollection()
    {
        if ($this->jsonDataIsset('optionCollection')) {
            return $this->getJsonPostData('optionCollection');
        }

        if (is_null($this->optionCollection)) {
            $this->optionCollection = GeodisOption::getCollection();
        }

        return $this->optionCollection;
    }

    protected function getCarrierOptionCollection()
    {
        if ($this->jsonDataIsset('carrierOptionCollection')) {
            return $this->getJsonPostData('carrierOptionCollection');
        }

        if (is_null($this->carrierOptionCollection)) {
            $this->carrierOptionCollection = GeodisCarrierOption::getCollection();
        }

        return $this->carrierOptionCollection;
    }

    protected function getGroupCarrierCollection()
    {
        if ($this->jsonDataIsset('groupCarrierCollection')) {
            return $this->getJsonPostData('groupCarrierCollection');
        }

        if (is_null($this->groupCarrierCollection)) {
            $this->groupCarrierCollection = GeodisGroupCarrier::getCollection();
        }

        return $this->groupCarrierCollection;
    }

    protected function getCarrierCollection()
    {
        if ($this->jsonDataIsset('carrierCollection')) {
            return $this->getJsonPostData('carrierCollection');
        }

        if (is_null($this->carrierCollection)) {
            $this->carrierCollection = GeodisCarrier::getCollection();
        }

        return $this->carrierCollection;
    }

    protected function getPrestaShopCarrierCollection()
    {
        if (is_null($this->prestashopCarrierCollection)) {
            $this->prestashopCarrierCollection = new PrestaShopCollection('Carrier');
            $this->prestashopCarrierCollection->where('deleted', '=', 0);
        }

        return $this->prestashopCarrierCollection;
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel//views/js/admin/GeodisConfigurationBack.js');
        $this->addCss(_PS_MODULE_DIR_.'geodisofficiel//views/css/admin/GeodisConfigurationBack.css');

        return parent::setMedia($isNewTheme);
    }

    public function getForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->fields_value['object_id'] = 0;

        $configurations = $this->getConfigurations();

        foreach ($configurations as $configuration) {
            $value = GeodisServiceConfiguration::getInstance()->getPostValue(
                $configuration['name'],
                GeodisServiceConfiguration::getInstance()->get($configuration['name'])
            );

            $helper->fields_value[$configuration['name']] = $value;
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.form.legend'),
                ),
                'input' => $configurations,
                'submit' => array(
                    'title' => GeodisServiceTranslation::get('Admin.GeneralConfiguration.index.submit.title'),
                    'class' => 'btn btn-default pull-right button',
                    'name' => 'submit',
                ),
            ),
        );

        $helper->languages = $this->context->controller->getLanguages();
        $helper->default_form_language = (int) $this->context->language->id;

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigurations()
    {
        $configurations = array();
        foreach (GeodisServiceConfiguration::getInstance()->getConfigurations() as $configuration) {
            if (!$configuration['general_configuration']) {
                continue;
            }

            $configurations[] = $configuration;
        }

        return $configurations;
    }

    public function processForm()
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $configuration) {
            $error = false;

            $value = GeodisServiceConfiguration::getInstance()->getPostValue($configuration['name']);

            if (isset($configuration['validator'])) {
                if (!call_user_func('Validate::'.$configuration['validator'], $value)) {
                    $error = true;
                }
            }

            if ($error) {
                if (!empty($configuration['error'])) {
                    $this->errors[] = $configuration['error'];
                } else {
                    $this->errors[] = (string) GeodisServiceTranslation::get(
                        'Admin.GeneralConfiguration.process.error.generic.%1$s.%2$s'
                    )
                    ->addVar($configuration['name'])
                    ->addVar($configuration['validator']);
                }
            } else {
                GeodisServiceConfiguration::getInstance()->set($configuration['name'], $value);
            }
        }

        $this->confirmations[] = GeodisServiceTranslation::get('Admin.GeneralConfiguration.post.message.success');
    }
}
