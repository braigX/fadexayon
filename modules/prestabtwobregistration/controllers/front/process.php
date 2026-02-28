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
class PrestaBtwoBRegistrationProcessModuleFrontController extends ModuleFrontController
{
    private $currentClassName = 'process';

    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        if ($action == 'displayCustomFeilds') {
            $this->displayAjaxDisplayCustomFeilds();
        } elseif ($action == 'checkStates') {
            $this->displayAjaxCheckStates();
        } else {
            $download = Tools::getValue('download');
            $id = Tools::getValue('id');
            $idCustomer = Tools::getValue('id_customer');
            if ($idCustomer && $id && $download) {
                $this->downloadFile($id, $idCustomer);
                exit;
            }
            $this->context->smarty->assign(
                array(
                    'TEXT' => PrestaBtwoBRegistrationHelper::TEXT,
                    'TEXTAREA' => PrestaBtwoBRegistrationHelper::TEXTAREA,
                    'DATE' => PrestaBtwoBRegistrationHelper::DATE,
                    'YESNO' => PrestaBtwoBRegistrationHelper::YESNO,
                    'MULTISELECT' => PrestaBtwoBRegistrationHelper::MULTISELECT,
                    'DROPDOWN' => PrestaBtwoBRegistrationHelper::DROPDOWN,
                    'CHECKBOX' => PrestaBtwoBRegistrationHelper::CHECKBOX,
                    'RADIO' => PrestaBtwoBRegistrationHelper::RADIO,
                    'FILE' => PrestaBtwoBRegistrationHelper::FILE,
                    'MESSAGE' => PrestaBtwoBRegistrationHelper::MESSAGE
                )
            );
        }
    }

    public function displayAjaxDisplayCustomFeilds()
    {
        $result = array();
        $idLang = (int) $this->context->language->id;
        $idCustomer = $this->context->customer->id;
        $registrationFields = new PrestaBtwoBRegistrationCustomFields();
        $fields = $registrationFields->getFieldByIdHeadingGroup(
            $this->context->language->id
        );
        $fieldInfo = array();
        if ($fields) {
            foreach ($fields as $fKey => $field) {
                $multipleValues = $registrationFields->getMultipleValuesById(
                    $field['id_presta_btwob_custom_fields'],
                    $this->context->language->id
                );
                if ($multipleValues) {
                    $fields[$fKey]['values'] = $multipleValues;
                }
                if (Tools::getValue('controller') === 'identity') {
                    $customerData = $objRegistrationValue->getFieldValueByIdField(
                        $field['id_presta_registration_fields'],
                        $this->context->customer->id
                    );
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::MULTISELECT
                        || $field['field_type'] === PrestaBtwoBRegistrationHelper::CHECKBOX
                    ) {
                        $value = Tools::jsonDecode($customerData['field_value']);
                    } else {
                        $value = $customerData['field_value'];
                    }

                    if ($value || $value == 0) {
                        $fields[$fKey]['selected_value'] = $value;
                        $fields[$fKey]['selected_value_id'] = $customerData['id_presta_btwob_registration_value'];
                    }
                }
            }
            $fieldInfo[$fKey]['fields'] = $fields;
        }
        $this->context->smarty->assign(
            array(
                'downloadLink' => $this->context->link->getModuleLink(
                    $this->module->name,
                    'process',
                    array(
                        'download' => 1,
                        'id_customer' => $idCustomer
                    )
                ),
                'presta_controller_name' => '',
                'presta_errors' => isset($this->customFieldErrors) ? $this->customFieldErrors : array(),
                'fieldInfo' => $fieldInfo,
                'TEXT' => PrestaBtwoBRegistrationHelper::TEXT,
                'TEXTAREA' => PrestaBtwoBRegistrationHelper::TEXTAREA,
                'DATE' => PrestaBtwoBRegistrationHelper::DATE,
                'YESNO' => PrestaBtwoBRegistrationHelper::YESNO,
                'MULTISELECT' => PrestaBtwoBRegistrationHelper::MULTISELECT,
                'DROPDOWN' => PrestaBtwoBRegistrationHelper::DROPDOWN,
                'CHECKBOX' => PrestaBtwoBRegistrationHelper::CHECKBOX,
                'RADIO' => PrestaBtwoBRegistrationHelper::RADIO,
                'FILE' => PrestaBtwoBRegistrationHelper::FILE,
                'MESSAGE' => PrestaBtwoBRegistrationHelper::MESSAGE
            )
        );
        $tpl = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.'/views/templates/front/custom_fields.tpl'
        );
        $result = array(
            'status' => 'ok',
            'tpl' => $tpl
        );
        die(json_encode($result));
    }

    public function displayAjaxGetDependantField()
    {
        $fieldId = Tools::getValue('fieldId');
        $currentValue = Tools::getValue('currentInputVal');
        $obj = new PrestaBtwoBRegistrationCustomFields();
        $dependantFields = $obj->getDependantFieldByIdDependant($fieldId, $currentValue, $this->context->language->id);
        if ($dependantFields) {
            $objRegistrationValue = new PrestaBtwoBRegistrationValue();
            foreach ($dependantFields as $key => $group) {
                if ($this->context->customer->id) {
                    $customerData = $objRegistrationValue->getFieldValueByIdField(
                        $group['id_presta_btwob_custom_fields'],
                        $this->context->customer->id
                    );
                    if ($customerData) {
                        if ($group['field_type'] === PrestaBtwoBRegistrationHelper::MULTISELECT ||
                        $group['field_type'] === PrestaBtwoBRegistrationHelper::CHECKBOX
                        ) {
                            $value = json_encode($customerData['field_value']);
                        } else {
                            $value = $customerData['field_value'];
                        }
                        if ($value) {
                            $dependantFields[$key]['selected_value'] = $value;
                            $dependantFields[$key]['selected_value_id'] = $customerData['id_presta_btwob_registration_value'];
                        }
                    }
                }
                $multipleValues = $obj->getMultipleValuesById($group['id_presta_btwob_custom_fields']);
                if ($multipleValues) {
                    $dependantFields[$key]['values'] = $multipleValues;
                }
            }
            $this->context->smarty->assign(
                array(
                    'currentFieldId' => $fieldId,
                    'dependantFields' => $dependantFields,
                    'front_controller' => Tools::getValue('front_controller')
                )
            );
            $depandent = $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/presta_dependant_field.tpl'
            );
            $result = array(
                'status' => 'ok',
                'tpl' => $depandent
            );
        } else {
            $result = array(
                'status' => 'ko'
            );
        }
        die(json_encode($result));
    }

    public function displayAjaxCheckStates()
    {
        $idCountry = (int) Tools::getValue('idCountry');
        $data = array();
        if ($idCountry) {
            $data = array(
                'status' => 'ok',
                'state_req' => Country::containsStates($idCountry),
                'dni_req' => Country::isNeedDniByCountryId($idCountry),
                'states' => State::getStatesByIdCountry($idCountry, true)
            );
        }

        die(json_encode($data));
    }

    public function downloadFile($id, $idCustomer)
    {
        $value = new PrestaBtwoBRegistrationValue($id);
        if (Validate::isLoadedObject($value) && $idCustomer) {
            $m = $this->module->name;
            $file = _PS_MODULE_DIR_ . $m . '/views/img/customer-custom-files/' . $idCustomer . '/' . $value->field_value;
            if (Tools::getIsset('download') && file_exists($file)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Type: application/octet-stream');
                header('Content-Type: application/force-download');
                header('Content-Description: File Transfer');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit;
            } else {
                Tools::redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
}
