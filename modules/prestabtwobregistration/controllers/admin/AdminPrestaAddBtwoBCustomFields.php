<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminPrestaAddBtwoBCustomFieldsController extends ModuleAdminController
{
    protected $position_identifier = 'id_presta_btwob_custom_fields';
    private $tabClassName = 'AdminPrestaAddBtwoBCustomFields';

    public function __construct()
    {
        $this->identifier = 'id_presta_btwob_custom_fields';
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'presta_btwob_custom_fields';
        $this->className = 'PrestaBtwoBRegistrationCustomFields';
        $this->list_no_link = true;
        $this->lang = false;
        $this->_defaultOrderBy = 'position';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_lang` rfhl
            ON (a.id_presta_btwob_custom_fields = rfhl.id_presta_btwob_custom_fields)';
        $this->_select = 'rfhl.field_title as title';
        $this->_where = ' AND rfhl.`id_lang` = ' . $this->context->language->id . ' AND a.`is_deleted` = 0';

        $this->fields_list = array(
            'id_presta_btwob_custom_fields' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'title' => array(
                'title' => $this->l('Field Title'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'field_type' => array(
                'title' => $this->l('Field Type'),
                'align' => 'center',
                'callback' => 'filterDisplayName'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'active' => 'status',
                'type'=> 'bool',
            ),
            'is_mandatory' => array(
                'title' => $this->l('Mandatory'),
                'align' => 'center',
                'active' => 'is_mandatory',
                'type'=> 'bool',
            ),
            'is_dependant' => array(
                'title' => $this->l('Dependant'),
                'align' => 'center',
                'type' => 'bool'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
            ),
            'date_add' => array(
                'title' => $this->l('Add Date'),
                'type' => 'date',
                'align' => 'right',
            ),
            'date_upd' => array(
                'title' => $this->l('Update Date'),
                'type' => 'date',
                'align' => 'right',
            ),
        );
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', [], 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
            ),
        );
        Media::addJsDef(
            array(
                'presta_admin_rf_multiple_value' => $this->context->link->getAdminLink(
                    'AdminPrestaAddBtwoBCustomFields',
                    true,
                    array()
                ),
            )
        );
    }

    public function getColorLabel($value, $row)
    {
        if ($value == 1) {
            return '<span style="color: green">Yes</span>';
        } else {
            return '<span style="color: red">No</span>';
        }
    }

    public function filterDisplayName($val)
    {
        return PrestaBtwoBRegistrationHelper::TYPELIST[$val];
    }

    public function initToolbar()
    {
        parent::initToolBar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new')
        );
        $this->page_header_toolbar_btn['desc-module-back'] = array(
            'href' => 'index.php?controller=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules'),
            'icon' => 'process-icon-back',
            'desc' => $this->l('Go to module manager')
        );
        $this->page_header_toolbar_btn['desc-module-reload'] = array(
            'href' => 'index.php?controller=' .
                $this->tabClassName .
                '&token=' .
                Tools::getAdminTokenLite($this->tabClassName) .
                '&reload=1',
            'icon' => 'process-icon-refresh',
            'desc' => $this->l('Refresh')
        );
        $this->page_header_toolbar_btn['desc-module-translate'] = array(
            'href' => '#',
            'desc' => $this->l('Translate'),
            'icon' => 'process-icon-flag',
            'modal_target' => '#moduleTradLangSelect'
        );
        $this->page_header_toolbar_btn['desc-module-hook'] = array(
            'href' => 'index.php?tab=AdminModulesPositions&token=' .
                Tools::getAdminTokenLite('AdminModulesPositions') . '&show_modules=' .
                Module::getModuleIdByName($this->module->name),
            'icon' => 'process-icon-anchor',
            'desc' => $this->l('Manage hooks'),
        );
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        switch ($this->display) {
            case '':
            case 'list':
                array_pop($this->toolbar_title);
                $this->toolbar_title[] = $this->l('Add B2B Custom Fields');
                break;
            case 'view':
            case 'add':
                array_pop($this->toolbar_title);
                    $this->toolbar_title[] = $this->l('Add new field');
                break;
            case 'edit':
                array_pop($this->toolbar_title);
                if (($rule = $this->loadObject(true)) && Validate::isLoadedObject($rule)) {
                    $this->toolbar_title[] = $this->l('Edit');
                } else {
                    $this->toolbar_title[] = $this->l('Add new field');
                }
                break;
        }
    }

    public function initModal()
    {
        parent::initModal();
        $languages = Language::getLanguages(false);
        $translateLinks = array();
        $module = Module::getInstanceByName($this->module->name);
        if (false === $module) {
            return;
        }
        $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
        $link = Context::getContext()->link;
        foreach ($languages as $lang) {
            if ($isNewTranslateSystem) {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslationSf',
                    true,
                    array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale']
                    )
                );
            } else {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslations',
                    true,
                    array(),
                    array(
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code']
                    )
                );
            }
        }
        $tabLink = 'index.php?tab=AdminTranslations&token=';
        $adminTranslation = Tools::getAdminTokenLite('AdminTranslations').'&type=modules&module=';
        $configure = $this->module->name.'&lang=';
        $this->context->smarty->assign(
            array(
                'trad_link' => $tabLink.$adminTranslation.$configure,
                'module_languages' => $languages,
                'module_name' => $this->module->name,
                'translateLinks' => $translateLinks
            )
        );
        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');
        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }

    public function renderForm()
    {
        $registrationFields = new PrestaBtwoBRegistrationCustomFields();
        $obj = $registrationFields->getAllNoticeMessage($this->context->language->id);
        $presta_message = array();
        foreach ($obj as $ob) {
            $presta_message = $ob['notice_message'];
        }
        $fieldType = array(
            PrestaBtwoBRegistrationHelper::YESNO,
            PrestaBtwoBRegistrationHelper::DROPDOWN,
            PrestaBtwoBRegistrationHelper::RADIO
        );
        $fieldType = "'" . implode ( "', '", $fieldType ) . "'";
        $fieldList = $registrationFields->getDependantField($fieldType);

        $this->context->smarty->assign(
            array(
                'presta_message' => $presta_message,
                'text_type' => PrestaBtwoBRegistrationHelper::TEXT,
                'textarea_type' => PrestaBtwoBRegistrationHelper::TEXTAREA,
                'type_fields' => PrestaBtwoBRegistrationHelper::TYPELIST,
                'multi_type_fields' => PrestaBtwoBRegistrationHelper::MULTITYPELIST,
                'fieldList' => $fieldList
            )
        );
        $registrationFields = new PrestaBtwoBRegistrationCustomFields();
        $id = Tools::getValue('id_presta_btwob_custom_fields');
        if ($id) {
            $registrationFields = new PrestaBtwoBRegistrationCustomFields($id);
            if (Validate::isLoadedObject($registrationFields)) {
                $multipleValues = $registrationFields->getMultipleValuesByFieldId($id);
                if ($multipleValues) {
                    foreach ($multipleValues as $key => $idMultiValue) {
                        $multipleLangData = $registrationFields->getMultipleValuesLangDataByFieldId(
                            $idMultiValue['id_multi_value']
                        );
                        $multipleValues[$key]['data'] = $multipleLangData;
                    }
                }
                if ($multipleValues) {
                    $registrationFields->{'multiple_values'} = $multipleValues;
                }
                $this->context->smarty->assign(
                    array(
                        'registrationFields' => $registrationFields,
                    )
                );
            }
            Media::addJsDef(
                array(
                    'currentId' => $id
                )
            );
        }
        Media::addJsDef(
            array(
                'presta_admin_dependant_link' => $this->context->link->getAdminLink(
                    'AdminPrestaAddBtwoBCustomFields',
                    true,
                    array()
                ),
                'currentId' => $id ? $id : 0,
            )
        );

        $this->context->smarty->assign(
            array(
                'urlBack' => $this->context->link->getAdminLink('AdminPrestaAddBtwoBCustomFields'),
                'typeList' => PrestaBtwoBRegistrationHelper::TYPELIST,
                'validationFields' => PrestaBtwoBRegistrationHelper::VALIDATIONFIELD,
                'languages' => Language::getLanguages(true),
                'current_lang' => $this->context->language,
            )
        );
        $this->fields_form = array(
            'submit' => array(
                'name' => 'submit',
            ),
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        parent::postProcess();
        $idCustomer = Tools::getValue('id_customer');
        $id = Tools::getValue('id');
        $download = Tools::getValue('download');
        if ($download && $id && $idCustomer) {
            $this->downloadCustomFile($id, $idCustomer);
        }

        $id = (int) Tools::getValue('id_presta_btwob_custom_fields');
        $registrationFields = new $this->className();
        if ($id) {
            $registrationFields = new $this->className($id);
        }
        if (Tools::isSubmit('submit') || Tools::isSubmit('staysubmit')) {
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            $languages = Language::getLanguages();
            $defaultLangObj = new Language($defaultLang);
            $id = Tools::getValue('presta_fields_id');
            $fieldTitle = trim(Tools::getValue('presta_rf_field_title_' . $defaultLang));
            $fieldType = Tools::getValue('field_type');
            $defaultValue = trim(Tools::getValue('presta_rf_default_field_' . $defaultLang));
            $fieldValidation = Tools::getValue('presta_field_validation');
            $maximumsize = Tools::getValue('maximum_size');
            $fileTypes = Tools::getValue('field_types');
            $noticeTypes = Tools::getValue('alert_types');
            $noticeMessage = trim(Tools::getValue('presta_message_fields_div' . $defaultLang));
            $isDependant = Tools::getValue('PRESTA_DEPENDANT_FIELDS');
            $idShopGroup = $this->context->shop->id_shop_group;
            if (!$fieldTitle) {
                $this->errors[] = sprintf(
                    $this->l('Field Title be empty in %s language.'),
                    $defaultLangObj->name
                );
            }
            if ($isDependant == 1) {
                $data = Tools::getValue('presta_check');
                if (isset($data) && $data == 1) {
                    $this->errors[] = $this->l('Dependant field should not be empty.');
                }
            }

            if ($fieldType === PrestaBtwoBRegistrationHelper::MESSAGE && !$noticeMessage) {
                if (!$noticeMessage) {
                    $this->errors[] = sprintf(
                        $this->l('Message cannot be empty in %s language.'),
                        $defaultLangObj->name
                    );
                }
                foreach ($languages as $lang) {
                    $noticeMessage = trim(Tools::getValue('presta_message_fields_div' . $lang['id_lang']));
                    if ($noticeMessage && !Validate::isLabel($noticeMessage)) {
                        $this->errors[] = sprintf(
                            $this->l('Message is not valid for %s language.'),
                            $lang['name']
                        );
                    }
                }
            }

            if (Tools::getValue('PRESTA_DEPENDANT_FIELDS')) {
                $dependantField = Tools::getValue('presta_dependant_field');
                $dependantValue = Tools::getValue('presta_dependant');
            } else {
                $dependantField = 0;
                $dependantValue = 0;
            }
            if ($fieldType === PrestaBtwoBRegistrationHelper::FILE && !$maximumsize) {
                $this->errors[] = $this->l('Size can not be empty');
            } elseif ($fieldType === PrestaBtwoBRegistrationHelper::FILE &&
                !Validate::isUnsignedInt($maximumsize)
            ) {
                $this->errors[] = $this->l('Size is not valid');
            }
            if ($fieldType === PrestaBtwoBRegistrationHelper::FILE && !$fileTypes) {
                $this->errors[] = $this->l('File can not be empty');
            } elseif ($fieldType === PrestaBtwoBRegistrationHelper::FILE &&
                !Validate::isCleanHtml($fileTypes)
            ) {
                $this->errors[] = $this->l('File is not valid');
            }

            if (empty($this->errors)) {
                $registrationFields = new PrestaBtwoBRegistrationCustomFields();
                if ($id) {
                    $registrationFields = new PrestaBtwoBRegistrationCustomFields($id);
                }
                foreach ($languages as $lang) {
                    $titleField = trim(Tools::getValue('presta_rf_field_title_' . $lang['id_lang']));
                    $defValue = trim(Tools::getValue('presta_rf_default_field_' . $lang['id_lang']));
                    $message = trim(Tools::getValue('presta_message_fields_div' . $lang['id_lang']));
                    if (!$titleField) {
                        $titleField = $fieldTitle;
                    }
                    if (!$defValue) {
                        $defValue = $defaultValue;
                    }
                    if (!$message) {
                        $message = $noticeMessage;
                    }
                    $registrationFields->field_title[$lang['id_lang']] = $titleField;
                    $registrationFields->default_value[$lang['id_lang']] = $defValue;
                    $registrationFields->notice_message[$lang['id_lang']] = $message;
                }
                $registrationFields->position = $this->className::getHigherPosition() + 1;
                $registrationFields->field_type = $fieldType;
                $registrationFields->field_validation = $fieldValidation;
                $registrationFields->maximum_size = $maximumsize;
                $registrationFields->file_types = str_replace(' ', '', $fileTypes);
                $registrationFields->notice_types = $noticeTypes;
                $registrationFields->active = (int) Tools::getValue('status');
                $registrationFields->is_mandatory = (int) Tools::getValue('is_required');
                $registrationFields->is_dependant = $isDependant;
                $registrationFields->id_dependant_field = $dependantField;
                $registrationFields->id_dependant_value = $dependantValue;
                if ($registrationFields->save()) {
                    $multiValuesIds = array();
                    $registrationFields->deleteMultipleValuesById($registrationFields->id);
                    if (array_key_exists($fieldType, PrestaBtwoBRegistrationHelper::MULTITYPELIST)) {
                        $langData =  array();
                        $langWiseData = array();
                        $defaultMultiValue = Tools::getValue('presta_registration_multi_value_field_' . $defaultLang);
                        if ($defaultMultiValue) {
                            foreach ($defaultMultiValue as $main_key => $defaultValue) {
                                Db::getInstance()->insert(
                                    'presta_btwob_custom_fields_value',
                                    array(
                                        'id_presta_btwob_custom_fields' => (int) $registrationFields->id,
                                    )
                                );
                                $multiValuesIds[] = Db::getInstance()->Insert_ID();
                                foreach ($languages as $lang) {
                                    $multiValue = Tools::getValue(
                                        'presta_registration_multi_value_field_' . $lang['id_lang']
                                    );
                                    foreach ($multiValue as $key => $data) {
                                        if (!$data) {
                                            $multiValue[$key] = $defaultMultiValue[$key];
                                        }
                                    }
                                    $langWiseData[$lang['id_lang']] = $multiValue;
                                }
                                $langData[$main_key] = $langWiseData;
                            }
                            foreach ($langData as $data) {
                                if ($data) {
                                    foreach($data as $idLang => $langValues) {
                                        foreach ($langValues as $key => $values) {
                                            Db::getInstance()->insert(
                                                'presta_btwob_custom_fields_value_lang',
                                                array(
                                                    'id_multi_value' => (int) $multiValuesIds[$key],
                                                    'value' => pSQL($values),
                                                    'id_lang' => $idLang,
                                                )
                                            );
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                    if (Tools::isSubmit('submit')) {
                        $url = self::$currentIndex.'&conf=4&token='.$this->token;
                    } else {
                        $url = $this->context->link->getAdminLink(
                            'AdminPrestaAddBtwoBCustomFields',
                            true,
                            array(),
                            array(
                                'add'.$this->table => ''
                            )
                        );
                    }
                    Tools::redirectAdmin($url);
                }
            }
        } else if(Tools::isSubmit('is_mandatory' . $this->table)) {
            $obj = new $this->className(Tools::getValue('id_' . $this->table));
            $obj->is_mandatory = $obj->is_mandatory ? 0 : 1;
            $obj->update();
            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
        }
    }

    public function ajaxProcessPrestaAddMultipleValueFields()
    {
        $result = array();
        $this->context->smarty->assign(
            array(
                'languages' => Language::getLanguages(false),
                'current_lang' => $this->context->language,
            )
        );
        $tpl = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/presta_registration_value_field.tpl'
        );
        if ($tpl) {
            $result = array(
                'status' => 'ok',
                'tpl' => $tpl
            );
        } else {
            $result = array(
                'status' => 'ko'
            );
        }
        die(json_encode($result));
    }

    public function downloadCustomFile($id, $idCustomer)
    {
        $value = new PrestaBtwoBRegistrationValue($id);
        if (Validate::isLoadedObject($value) && $idCustomer) {
            $m = $this->module->name;
            $file = _PS_MODULE_DIR_ . $m . '/views/img/customer-custom-files/' . $idCustomer . '/' . $value->field_value;
            if (Tools::getIsset('download') && file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                flush(); // Flush system output buffer
                readfile($file);
                die();
            } else {
                Tools::redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function processDelete()
    {
        $id = Tools::getValue($this->identifier);
        $object = new $this->className($id);
        if (Validate::isLoadedObject($object)) {
            $object->is_deleted = 1;
            $object->update();
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $idConf = (int) (Tools::getValue('id'));
        $positions = Tools::getValue($this->table);
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int) $pos[2] === $idConf) {
                if ($objRuleConfig = new $this->className($idConf)) {
                    if (isset($position) && $objRuleConfig->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for carrier ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update carrier '.
                            (int) $idConf.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This carrier (' . (int) $idConf . ') can not be loaded"}';
                }
                break;
            }
        }
    }

    public function displayAjaxGetDepandantValue()
    {
        $id = Tools::getValue('id');
        $currentId = Tools::getValue('currentId');
        $obj = new $this->className($id);
        $objCurrentId = new $this->className($currentId);
        $result = array();
        if (Validate::isLoadedObject($obj)) {
            if ($obj->field_type === PrestaBtwoBRegistrationHelper::YESNO) {
                $multipleValues = array(
                    '0' => array(
                        'id_multi_value' => 0,
                        'value' => $this->l('No'),
                        'is_default_value' => 0
                    ),
                    '1' => array(
                        'id_multi_value' => 1,
                        'value' => $this->l('Yes'),
                        'is_default_value' => 1
                    )
                );
                $this->context->smarty->assign(
                    array(
                        'field_type' => 'radio'
                    )
                );
            } else {
                $multipleValues = $obj->getMultipleValuesById($id, $this->context->language->id);
            }
            $this->context->smarty->assign(
                array(
                    'objCurrentId' => $objCurrentId,
                    'multipleValues' => $multipleValues
                )
            );
            $result = array(
                'status' => 'ok',
                'tpl' => $this->context->smarty->fetch(
                    _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/dependant.tpl'
                )
            );
        } else {
            $result = array(
                'status' => 'ko'
            );
        }
        die(json_encode($result));
    }

    public function setMedia($isNewTheme = false)
    {
        if ($isNewTheme || !$isNewTheme) {
            parent::setMedia(false);
            $this->context->controller->addJs(
                _MODULE_DIR_ . $this->module->name . '/views/js/admin/presta_btwob_custom_field.js'
            );
            $this->context->controller->addCss(
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/presta_btwob_custom_field.css'
            );
        }
    }
}
