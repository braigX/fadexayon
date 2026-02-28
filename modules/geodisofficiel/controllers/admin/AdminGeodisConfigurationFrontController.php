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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';

class AdminGeodisConfigurationFrontController extends GeodisControllerAdminAbstractMenu
{
    protected $groupCarrierCollection;
    protected $confirmation;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function processSave()
    {
        foreach ($this->getFields() as $field) {
            switch ($field['source']) {
                case 'configuration':
                    $this->saveConfiguration($field);
                    break;
                case 'carrier':
                    $this->saveCarrierValue($field);
                    break;
                case 'logo':
                    $this->saveLogo($field);
                    break;
                case 'static':
                    break; // Nothing to do
                default:
                    throw new Exception('Invalid source "'.$field['source'].'"');
            }
        }

        if (empty($this->errors)) {
            // Move all tmp logo
            foreach ($this->getFields() as $field) {
                switch ($field['source']) {
                    case 'logo':
                        $this->moveLogo($field);
                        break;
                }
            }
        }

        if (empty($this->errors)) {
            $this->confirmations[] = GeodisServiceTranslation::get(
                'Admin.ConfigurationFront.submit.success'
            );
        }
    }

    protected function moveLogo($field)
    {
        try {
            $carrier = $field['carrier']->getCarrier(false);
            if (!GeodisServiceConfiguration::getInstance()->get('use_white_label')) {
                $relativeTmpPath = $field['carrier']->getDefaultLogoPath();
            } else {
                $relativeTmpPath = _PS_SHIP_IMG_DIR_.'/'.((int) $carrier->id).'.tmp.jpg';
            }

            $relativePath = ((int) $carrier->id).'.jpg';
            if (file_exists($relativeTmpPath)) {
                if (!GeodisServiceConfiguration::getInstance()->get('use_white_label')) {
                    copy($relativeTmpPath, _PS_SHIP_IMG_DIR_.'/'.$relativePath);
                    $oldRelativeTmpPath = _PS_SHIP_IMG_DIR_.'/'.((int) $carrier->id).'.tmp.jpg';
                    if (file_exists($oldRelativeTmpPath)) {
                        unlink($oldRelativeTmpPath);
                    }
                } else {
                    ImageManager::thumbnail(
                        $relativeTmpPath,
                        'carrier_'.((int) $carrier->id).'.jpg',
                        GeodisServiceConfiguration::getInstance()->get('carrier_logo_width')
                    );

                    if (file_exists($relativeTmpPath)) {
                        unlink($relativeTmpPath);
                    }

                    rename(_PS_TMP_IMG_DIR_.'carrier_'.
                        ((int) $carrier->id).'.jpg', _PS_SHIP_IMG_DIR_.'/'.$relativePath);
                }
            }
        } catch (Exception $e) {
            $this->errors[] = (string) GeodisServiceTranslation::get(
                'Admin.ConfigurationFront.submit.fileError.unknow'
            );
        }
    }

    protected function saveConfiguration($field)
    {
        GeodisServiceConfiguration::getInstance()->set($field['name'], $this->getValue($field));
    }

    protected function saveCarrierValue($field)
    {
        $carrier = $field['carrier']->getCarrier();
        $attribute = $field['attribute'];
        $carrier->$attribute = $this->getValue($field);
        try {
            $carrier->save();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    protected function saveCarrier($field)
    {
        GeodisServiceLog::getInstance()->error('etape 0');
        $currentCarrier = $field['carrier']->getCarrier();
        $groups = $currentCarrier->getGroups();
        $groupIds = array();
        foreach ($groups as $id) {
            $groupIds[] = $id['id_group'];
        }

        try {
            $newCarrier = $currentCarrier->duplicateObject();
            $newCarrier->copyCarrierData((int) $currentCarrier->id);
            $newCarrier->default = false;
            $newCarrier->save();
            $newCarrier->setGroups($groupIds);
        } catch (Exception $e) {
             $this->errors[] = $e->getMessage();
        }
           $currentCarrier->deleted = true;
         $currentCarrier->save();
        GeodisServiceLog::getInstance()->error("return");
        return $newCarrier->id;
    }

    protected function saveLogo($field)
    {
        if (!isset($_FILES[$field['name']])) {
            return;
        }

        // No file uploaded
        if ($_FILES[$field['name']]['error'] == UPLOAD_ERR_NO_FILE) {
            return;
        }

        switch ($_FILES[$field['name']]['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $this->errors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.submit.fileError.uploadMaxFileSize'
                );
                return;
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.submit.fileError.maxFileSize'
                );
                return;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->errors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.submit.fileError.noTmpDir'
                );
                return;
            case UPLOAD_ERR_CANT_WRITE:
                $this->errors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.submit.fileError.nonWritableDir'
                );
                return;
            case UPLOAD_ERR_OK:
                $ext = Tools::strtolower(pathinfo($_FILES[$field['name']]['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) {
                    $this->errors[] = (string) GeodisServiceTranslation::get(
                        'Admin.ConfigurationFront.submit.fileError.invalidExtension'
                    );
                    return;
                }

                try {
                    $newCarrierId = $this->saveCarrier($field);
                    $relativePath = (int) $newCarrierId.'.tmp.jpg';
                    copy($_FILES[$field['name']]['tmp_name'], _PS_SHIP_IMG_DIR_.'/'.$relativePath);
                } catch (Exception $e) {
                    $this->errors[] = (string) GeodisServiceTranslation::get(
                        'Admin.ConfigurationFront.submit.fileError.unknow'
                    );
                }
                return;
            default:
                $this->errors[] = (string) GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.submit.fileError.unknow'
                );
                return;
        }
    }

    public function renderList()
    {
        $this->base_tpl_view = 'main.tpl';
        if (!isset($this->tpl_view_vars)) {
            $this->tpl_view_vars = array();
        }

        $this->tpl_view_vars['groupCarrierCollection'] = $this->getGroupCarrierCollection();
        $this->tpl_view_vars['use_white_label'] = GeodisServiceConfiguration::getInstance()
            ->get('use_white_label');
        $this->tpl_view_vars['idLang'] = Context::getContext()->language->id;
        $this->tpl_view_vars['content'] = $this->getForm();

        $this->assignDefaultValues();
        $this->assignCurrentValues();


        return parent::renderView();
    }

    protected function assignCurrentValues()
    {
        $current = array();
        foreach ($this->getGroupCarrierCollection() as $groupCarrier) {
            $current[] = array(
                'id' => $groupCarrier->id,
                'name' => $groupCarrier->getCarrier()->name,
                'delay' => $groupCarrier->getCarrier()->delay,
                'logo' => $groupCarrier->getCarrierLogo(true),
            );
        }

        $this->tpl_view_vars['currentValuesJson'] = json_encode($current);
    }

    protected function assignDefaultValues()
    {
        $default = array();
        foreach ($this->getGroupCarrierCollection() as $carrier) {
            $default[] = array(
                'id' => $carrier->id,
                'name' => $carrier->getDefaultName(),
                'delay' => $carrier->getDefaultDelay(),
                'logo' => $carrier->getDefaultLogoUrl(),
            );
        }

        $this->tpl_view_vars['defaultValuesJson'] = json_encode($default);
    }

    protected function getValue($field)
    {
        switch ($field['source']) {
            case 'configuration':
                return $this->getConfigurationValue($field['name']);
            case 'carrier':
                return $this->getCarrierValue(
                    $field['name'],
                    $field['attribute'],
                    $field['carrier'],
                    isset($field['lang']) ? $field['lang'] : false
                );
            case 'logo':
                return $this->getLogoValue($field['carrier']);
            case 'static':
                // Case of custom attributes (ex: form separator, sub-title)
                return isset($field['value']) ? $field['value'] : '';
            default:
                throw new Exception('Invalid source "'.$field['source'].'"');
        }
    }

    protected function getConfigurationValue($name)
    {
        return GeodisServiceConfiguration::getInstance()->getPostValue(
            $name,
            GeodisServiceConfiguration::getInstance()->get($name)
        );
    }

    protected function getCarrierValue($name, $attribute, $carrier, $lang)
    {
        $issetPostValue = false;

        if ($lang) {
            $postValue = array();
            foreach (Language::getLanguages() as $lang) {
                $issetPostValue |= Tools::getIsset($name.'_'.$lang['id_lang']);
                $postValue[$lang['id_lang']] = Tools::getValue($name.'_'.$lang['id_lang']);
            }
        } else {
            $issetPostValue = Tools::getIsset($name);
            $postValue = Tools::getValue($name);
        }

        $current = $carrier->getCarrier()->$attribute;

        if ($issetPostValue) {
            return $postValue;
        } else {
            return $current;
        }
    }

    protected function getLogoValue($carrier)
    {
        return $carrier->getCarrierLogo(true);
    }

    protected function getCarrierById($id)
    {
        foreach ($this->getGroupCarrierCollection()->getResults() as $carrier) {
            if ($carrier->getCarrier()->id = $id) {
                return $carrier;
            }
        }

        throw new Exception('Invalid carrier ID "'.$id.'"');
    }

    protected function getForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->fields_value['object_id'] = 0;

        $fields = $this->getFields();

        foreach ($fields as $field) {
            $helper->fields_value[$field['name']] = $this->getValue($field);
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.form.legend'),
                ),
                'input' => $fields,
                'submit' => array(
                    'title' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.submit.title'),
                    'class' => 'btn btn-default pull-right button',
                    'name' => 'submit',
                ),
            ),
        );

        $helper->languages = $this->context->controller->getLanguages();
        $helper->default_form_language = (int) $this->context->language->id;

        return $this->confirmation.$helper->generateForm(array($fields_form));
    }

    public function getFields()
    {
        $fields = array();

        $fields[] = array(
            'name' => 'use_white_label',
            'source' => 'configuration',
            'type' => 'switch',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => GeodisServiceTranslation::get('*.*.switch.enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => GeodisServiceTranslation::get('*.*.switch.disabled'),
                ),
            ),
            'label' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.useWhiteLabel.label'),
            'desc' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.useWhiteLabel.desc'),
        );

        foreach ($this->getGroupCarrierCollection()->getResults() as $carrier) {
            // Separator
            $fields[] = array(
                'name' => 'seperator',
                'type' => 'html',
                'html_content' => '<br><br>',
                'source' => 'static',
            );

            // Title
            $title = GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrier.title.%s');
            $title->addVar($carrier->getDefaultName());
            $fields[] = array(
                'name' => 'title',
                'type' => 'html',
                'html_content' => '<h2>'.$title.'</h2>',
                'source' => 'static',
            );

            // Name
            $fields[] = array(
                'name' => 'name_'.$carrier->id,
                'idCarrier' => $carrier->id,
                'carrier' => $carrier,
                'attribute' => 'name',
                'source' => 'carrier',
                'type' => 'text',
                'validator' => 'isCarrierName',
                'label' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierName.label'),
                'desc' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierName.desc'),
            );

            // Description
            $fields[] = array(
                'name' => 'delay_'.$carrier->id,
                'idCarrier' => $carrier->id,
                'carrier' => $carrier,
                'attribute' => 'delay',
                'source' => 'carrier',
                'type' => 'text',
                'validator' => 'isGenericName',
                'lang' => true,
                'label' => GeodisServiceTranslation::get(
                    'Admin.ConfigurationFront.index.carrierDescription.label'
                ),
                'desc' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierDescription.desc'),
            );

            // Active
            $fields[] = array(
                'name' => 'active_'.$carrier->id,
                'idCarrier' => $carrier->id,
                'carrier' => $carrier,
                'attribute' => 'active',
                'source' => 'carrier',
                'type' => 'switch',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => GeodisServiceTranslation::get('*.*.switch.enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => GeodisServiceTranslation::get('*.*.switch.disabled'),
                    ),
                ),
                'label' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierActive.label'),
                'desc' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierActive.desc'),
            );

            // Logo
            $label = $this->getRenderLogo($carrier);

            /*
            $fields[] = array(
                'name' => 'logo',
                'type' => 'html',
                'html_content' => $content,
                'source' => 'static',
            );
             */
            $fields[] = array(
                'name' => 'logo_'.$carrier->id,
                'idCarrier' => $carrier->id,
                'carrier' => $carrier,
                'source' => 'logo',
                'type' => 'file',
                'label' => $label,
                'desc' => GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierLogo.desc'),
            );
        }

        return $fields;
    }

    public function getRenderLogo($carrier)
    {
        $path = $this->getLogoValue($carrier);

        if (!$path) {
            return GeodisServiceTranslation::get('Admin.ConfigurationFront.index.carrierLogo.label');
        }

        if (GEODIS_MODULE_NAME === 'geodisofficiel') {
            $tpl = $this->context->controller->createTemplate(
                '../../../../modules/'.GEODIS_MODULE_NAME
                .'/views/templates/admin/geodis_configuration_front/helpers/logo.tpl'
            );
        } else {
            $tpl = $this->context->controller->createTemplate(
                '../../../../modules/'.GEODIS_MODULE_NAME
                .'/views/templates/admin/france_express_configuration_front/helpers/logo.tpl'
            );
        }

        $this->context->smarty->assign(
            'path',
            $path
        );

        return $tpl->fetch();
    }

    public function getGroupCarrierCollection()
    {
        if (!$this->groupCarrierCollection) {
            $this->groupCarrierCollection = GeodisGroupCarrier::getCollection();
            $this->groupCarrierCollection->where('id_reference_carrier', '>', 0);
            $this->groupCarrierCollection->where('active', '=', 1);
        }

        return $this->groupCarrierCollection;
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel//views/js/admin/GeodisConfigurationFront.js');

        return parent::setMedia($isNewTheme);
    }
}
