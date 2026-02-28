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
include_once 'classes/PrestaBtwoBRegistrationClasses.php';

class PrestaBTwoBRegistration extends Module
{
    public $customFieldErrors = array();
    public $absoluteImagePath;
    public $relativeImagePath;
    public $imageType = array();

    public function __construct()
    {
        $this->name = 'prestabtwobregistration';
        $this->tab = 'front_office_features';
        $this->version = '8.0.0';
        $this->author = 'presta_world';
        $this->module_key = '57f01e1ee595ba93f1e606280810f816';
        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        );
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('B2B Registration');
        $this->description = $this->l(
            'Manage the B2B signups easily. Approve or disapprove the registration requests. Get notified when new B2B
            signups are made. Everything with the feature-rich B2B Registration Form for Prestashop'
        );
        $this->absoluteImagePath = _PS_MODULE_DIR_ . $this->name . '/views/img/customer-custom-files/';
        $this->relativeImagePath = _MODULE_DIR_ . $this->name . '/views/img/customer-custom-files/';
        $this->imageType = array(
            'jpg',
            'jpeg',
            'jfif',
            'pjpeg',
            'pjp',
            'png',
            'svg',
            'webp',
            'gif',
            'avif',
            'apng'
        );
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPrestaConfiguration'));
    }

    public function hookDisplayHeader()
    {
        $currentPage = Tools::getValue('controller');
        $allowedPage = array(
            'order',
            'authentication',
            'registration',
            'login',
            'address'
        );
        $registrationConfigure = $this->getModuleConfiguration();

        if (in_array($currentPage, $allowedPage)) {
            $vatRequired = false;
            if ($registrationConfigure->vat_number && $registrationConfigure->required_vat_number) {
                $vatRequired = true;
            }
            Media::addJsDef(
                array(
                    'presta_btob_process_url' => $this->context->link->getModuleLink($this->name, 'process'),
                    'presta_hide_dob' => $registrationConfigure->date_of_birth,
                    'presta_hide_siret' => $registrationConfigure->identification_siret_number,
                    'presta_please_choose' => $this->l('Please choose'),
                    'presta_enable_btob' => $registrationConfigure->enable_b2b,
                    'presta_enable_address' => $registrationConfigure->address,
                    'presta_current_page' => $currentPage,
                    'presta_required_vat' => $vatRequired,
                )
            );
        }
    }

    public function hookDisplayPersonalInformationTop()
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->enable_b2b
            && (!$this->context->customer->logged || $this->context->customer->logged == false)
            && !$this->context->customer->id
        ) {
            $this->context->smarty->assign(
                array(
                    'prestaConfigData' => $registrationConfigure,
                    'presta_btwob_top_link' => $this->context->link->getPageLink('registration', true),
                )
            );
            return $this->display(__FILE__, 'presta_personal_information_top.tpl');
        }
    }

    //CUSTOM FIELDS
    public function hookDisplayCustomerAccountForm()
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->enable_b2b) {
            $idCustomer = $this->context->customer->id;
            $customer = new Customer((int) $idCustomer);
            $registrationFields = new PrestaBtwoBRegistrationCustomFields();
            $fieldInfo = array();
            $fields = $registrationFields->getFieldByIdHeadingGroup($this->context->language->id);
            if ($fields) {
                $objRegistrationValue = new PrestaBtwoBRegistrationValue();
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
                            $field['id_presta_btwob_custom_fields'],
                            $this->context->customer->id
                        );
                        if ($customerData) {
                            if ($field['field_type'] === PrestaBtwoBRegistrationHelper::MULTISELECT
                                || $field['field_type'] === PrestaBtwoBRegistrationHelper::CHECKBOX
                            ) {
                                $value = json_decode($customerData['field_value']);
                            } else {
                                $value = $customerData['field_value'];
                            }
                            if ($value || $value == 0) {
                                $fields[$fKey]['selected_value'] = $value;
                                $fields[$fKey]['selected_value_id'] = $customerData['id_presta_btwob_registration_value'];
                            }
                        }
                    }
                }
                $fieldInfo[$fKey]['fields'] = $fields;
            }

            $availableGroups = $registrationConfigure->selected_groups;
            $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
            if ($availableGroups) {
                $availableGroups = json_decode($availableGroups);
                if ($availableGroups) {
                    foreach ($groups as $key => $group) {
                        if (!in_array($group['id_group'], $availableGroups)) {
                            unset($groups[$key]);
                        }
                    }
                }
            }
            $this->context->smarty->assign(
                array(
                    'presta_groups' => $groups,
                    'presta_custom_field_heading' => $registrationConfigure->custom_field_heading,
                    'presta_default_customer_group' => $customer->id_default_group,
                    'registrationConfigure' => $registrationConfigure,
                    'downloadLink' => $this->context->link->getModuleLink(
                        $this->name,
                        'process',
                        array(
                            'download' => 1,
                            'id_customer' => $idCustomer
                        )
                    ),
                    'presta_recaptcha_process' => $this->context->link->getModuleLink(
                        $this->name,
                        'recaptcha',
                        array(
                            'rand'=> rand()
                        )
                    ),
                    'presta_controller_name' => Tools::getValue('controller'),
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

            return $this->display(__FILE__, 'presta_registration_heading.tpl');
        }
    }

    public function hookActionSubmitAccountBefore($params)
    {
        $registrationConfigure = $this->getModuleConfiguration();
        $btobReg = (int) Tools::getValue('prestaBtoBRegistration');

        if ($registrationConfigure->enable_b2b && $btobReg) {
            if ($this->prestaValidateCustomField($registrationConfigure) == false) {
                return false;
            }
            if ($registrationConfigure->enable_google_recaptcha) {
                $this->validatereCaptcha($this->context->controller->errors, $registrationConfigure);
                if (empty($this->context->controller->errors)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    public function validatereCaptcha(&$errors, $registrationConfigure)
    {
        if (empty($errors)) {
            if (empty($errors)) {
                if ($registrationConfigure->recaptcha_type == 2) {
                    if (Tools::getIsset('presta_imgcaptcha')) {
                        $captcha = trim(Tools::getValue('presta_imgcaptcha'));
                        if (!$captcha) {
                            $errors[] = $this->l('Captcha is missing!');
                        } elseif (isset($this->context->cookie->presta_pppppcaptcha)
                            && strcasecmp($this->context->cookie->presta_pppppcaptcha, $captcha) != 0
                        ) {
                            $errors[] = $this->l('Entered captcha code does not match!');
                        }
                    } else {
                        $errors[] = $this->l('Invalid Captch');
                    }
                } else {
                    $gResponse = Tools::getValue('g-recaptcha-response');
                    if ($gResponse) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        if ($registrationConfigure->recaptcha_type == 1) {
                            $key = $registrationConfigure->secret_key;
                        }
                        $url = 'https://www.google.com/recaptcha/api/siteverify';
                        $response = Tools::file_get_contents(
                            $url . '?secret=' . $key . '&response=' . $gResponse . '&remoteip=' . $ip
                        );
                        $data = json_decode($response);
                        if (isset($data->success) &&  $data->success === true) {
                            // OK
                        } else {
                            $errors[] = $this->l('Invalid reCaptch');
                        }
                    } else {
                        $errors[] = $this->l('Invalid reCaptch');
                    }
                }
            }
        }
    }

    public function prestaValidateCustomField($registrationConfigure)
    {
        $registrationFields = new PrestaBtwoBRegistrationCustomFields();
        $allFields = $registrationFields->getAllRegistrationFields();
        $btobReg = Tools::getValue('prestaBtoBRegistration');
        if ($btobReg) {
            if ($allFields && $registrationConfigure->enable_custom_fields) {
                foreach ($allFields as $field) {
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::MESSAGE) {
                        continue;
                    }
                    $customId = 'presta_custom_' . $field['id_presta_btwob_custom_fields'];
                    $value = Tools::getValue($customId);

                    if ($field['is_dependant']) {
                        $dependantCustomId = 'presta_custom_' . $field['id_dependant_field'];
                        $dependantValue = Tools::getValue($dependantCustomId);
                        if ($dependantCustomId != $value) {
                            continue;
                        }
                    }
                    if (!is_array($value)) {
                        $value = trim($value);
                    }
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::YESNO
                        && $field['is_mandatory']
                        && ($value == '0' || $value == '1')
                    ) {
                        continue;
                    }
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::CHECKBOX
                        && $field['is_mandatory']
                        && !$value
                    ) {
                        $this->customFieldErrors[$customId] = sprintf(
                            $this->l('%s can not be empty'),
                            $field['field_title']
                        );
                    } elseif (($value !== 0 && !$value) &&
                        $field['is_mandatory'] &&
                        $field['field_type'] !== PrestaBtwoBRegistrationHelper::FILE
                    ) {
                        $this->customFieldErrors[$customId] = sprintf(
                            $this->l('%s can not be empty'),
                            $field['field_title']
                        );
                    }
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::TEXT ||
                        $field['field_type'] === PrestaBtwoBRegistrationHelper::TEXTAREA
                    ) {
                        if ($value) {
                            if ($field['field_validation'] === PrestaBtwoBRegistrationHelper::DECIMALNUMBER) {
                                if (!PrestaBtwoBRegistrationHelper::isDecimal($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s should be decimal only.'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::INTEGERNUMBER) {
                                if (!Validate::isInt($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be integer only.'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::EMAILADDRESS) {
                                if (!Validate::isEmail($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be email type.'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::WEBSITEURLADDRESS) {
                                if (!Validate::isUrl($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be URL'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::ALPHABETONLY) {
                                if (!Validate::isName($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be contains only letters'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::ALPHABETNUMERICONLY) {
                                if (!Validate::isConfigName($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be contains only letters and numbers'),
                                        $field['field_title']
                                    );
                                }
                            } elseif ($field['field_validation'] === PrestaBtwoBRegistrationHelper::DATEONLY) {
                                if (!Validate::isDate($value)) {
                                    $this->customFieldErrors[$customId] = sprintf(
                                        $this->l('%s must be date type(Y-m-d)'),
                                        $field['field_title']
                                    );
                                }
                            }
                        }
                    } elseif ($field['field_type'] === PrestaBtwoBRegistrationHelper::DATE) {
                        if (!Validate::isDate($value) && $field['is_mandatory']) {
                            $this->customFieldErrors[$customId] = sprintf(
                                $this->l('%s must be date type'),
                                $field['field_title']
                            );
                        }
                    } elseif ($field['field_type'] === PrestaBtwoBRegistrationHelper::FILE) {
                        $fileField = isset($_FILES[$customId]) ? $_FILES[$customId] : false;
                        if ($fileField) {
                            $ext = pathinfo($fileField['name'], PATHINFO_EXTENSION);
                            $allowedType = explode(',', $field['file_types']);
                            $fileSize = Tools::ps_round($fileField['size']/1000, 2); // file size in kb
                            if (($fileField['size'] <= 0 || !$fileField['size']) && $field['is_mandatory']) {
                                $this->customFieldErrors[$customId] = sprintf(
                                    $this->l('%s can not be empty'),
                                    $field['field_title']
                                );
                            } elseif ($fileSize && $fileSize > $field['maximum_size']) {
                                $this->customFieldErrors[$customId] = sprintf(
                                    $this->l('%s file size must be less than %s Kb.'),
                                    $field['field_title'],
                                    $field['maximum_size']
                                );
                            } elseif ($fileSize && !in_array($ext, $allowedType)) {
                                $this->customFieldErrors[$customId] = sprintf(
                                    $this->l('Allowed extension are only - %s.'),
                                    $field['file_types']
                                );
                            }
                        }
                    }
                }
            }
        }
        if (!empty($this->customFieldErrors)) {
            return false;
        } else {
            return true;
        }
    }

    public function hookDisplayCustomerAccountFormTop()
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->enable_b2b) {
            $this->context->smarty->assign(
                array(
                    'prestaConfigData' => $registrationConfigure,
                    'presta_btwob_top_link' => $this->context->link->getPageLink('registration', true),
                )
            );
            return $this->display(__FILE__, 'presta_display_customer_account_form_top.tpl');
        }
    }

    public function getModuleConfiguration()
    {
        $registrationConfiguration = new PrestaBtwoBRegistrationConfiguration();
        $configuration = $registrationConfiguration->getConfigId();
        return new PrestaBtwoBRegistrationConfiguration($configuration, $this->context->language->id);
    }

    public function sendMailToAdminBtoB($customer, $adminMail)
    {
        $data = array(
            '{customer_id}' => $customer->id,
            '{presta_message}' => $this->l('New customer has just created an account on your website at '),
            '{presta_date}' => $customer->date_add,
            '{name}' => $customer->firstname . ' ' . $customer->lastname,
            '{email}' => $customer->email,
        );
        if (Validate::isEmail($adminMail)) {
            return Mail::Send(
                (int)$this->context->language->id,
                'presta_send_mail_to_admin', // Email template
                $this->l('B2B registration.'), // Subject of email
                $data,
                $adminMail,
                null,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . $this->name . '/mails/',
                false,
                null,
                null
            );
        }
    }

    // This function is used to send email on customer approved form.
    public function sendMailToCustomerForAdminApproved($data, $customer_email)
    {
        return Mail::Send(
            (int)$this->context->language->id,
            'presta_send_mail_to_customer', // Email template
            $this->l('B2B registration.'), // Subject of email
            $data,
            $customer_email,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . $this->name . '/mails/',
            false,
            null,
            null
        );
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $currentPage = Tools::getValue('controller');
        $allowedController = array('authentication', 'order', 'registration');
        $registrationConfigure = $this->getModuleConfiguration();
        if (in_array($currentPage, $allowedController) && $registrationConfigure->enable_b2b) {
            $idCustomer = $params['newCustomer']->id;
            $isActive = $registrationConfigure->b2b_customer_auto_approval;
            $customer = new Customer($idCustomer);
            if (Tools::getValue('prestaBtoBRegistration')) {
                $obj = new PrestaBtwoBRegistrationManageBtwoBCustomer();
                $obj->id_customer = $idCustomer;
                $obj->is_validated = $isActive ? 1 : 0;
                $obj->save();
                if (($registrationConfigure->address == 1) &&
                    ($currentPage === 'authentication' || $currentPage === 'registration')) {
                    $this->createCustomerAddress($idCustomer);
                }
                if ($registrationConfigure->enable_custom_fields) {
                    $this->saveCustomerCustomFieldData($idCustomer);
                }
                if ($registrationConfigure->enable_group_selection_one) {
                    if ($registrationConfigure->enable_group_selection) {
                        $idCustomerGroup = Tools::getValue('customer_group');
                    } else {
                        $idCustomerGroup = $registrationConfigure->assign_groups;
                    }
                    $helper = new PrestaBtwoBRegistrationHelper();
                    $helper->addCustomerIntoDesireGroup($idCustomer, $idCustomerGroup);
                }
                if ($registrationConfigure->send_email_notification_admin && $registrationConfigure->admin_email_id) {
                    $this->sendMailToAdminBtoB($customer, $registrationConfigure->admin_email_id);
                }
                if ($registrationConfigure->b2b_customer_auto_approval == 0) {
                    $this->deactivateCustomerAccount($idCustomer);
                }
            }
        }
    }

    public function createCustomerAddress($idCustomer)
    {
        $firstname = trim(Tools::getValue('firstname'));
        $lastname = trim(Tools::getValue('lastname'));
        $company = trim(Tools::getValue('company'));
        $vatNumber = trim(Tools::getValue('vat_number'));
        $address1 = trim(Tools::getValue('address1'));
        $address2 = trim(Tools::getValue('address2'));
        $city = trim(Tools::getValue('city'));
        $postcode = trim(Tools::getValue('postcode'));
        $id_state = trim(Tools::getValue('id_state'));
        $id_country = trim(Tools::getValue('id_country'));
        $phone = trim(Tools::getValue('phone'));
        $customer_group = trim(Tools::getValue('customer_group'));
        $alias = trim(Tools::getValue('alias'));
        $dni = trim(Tools::getValue('dni'));
        if (!Country::isNeedDniByCountryId($id_country)) {
            $dni = '';
        }
        if (!$alias) {
            $alias = $this->l('My Address');
        }
        $address = new Address(Tools::getValue('id_address'),$this->context->language->id);
        $customer = new Customer($idCustomer);
        $address->id_customer = $idCustomer;
        $address->alias = $alias;
        $address->firstname = $firstname;
        $address->lastname = $lastname;
        $address->company = $company;
        $address->vat_number = $vatNumber;
        $address->address1 = $address1;
        $address->address2 = $address2;
        $address->city = $city;
        $address->postcode = $postcode;
        $address->id_state = $id_state;
        $address->id_country = $id_country;
        $address->phone = $phone;
        $address->dni = $dni;
        $customer->id_default_group = $customer_group;
        $address->save();
    }

    public function deactivateCustomerAccount($idCustomer)
    {
        $registrationConfigure = $this->getModuleConfiguration();
        $isActive = (int) $registrationConfigure->b2b_customer_auto_approval;
        $customer = new Customer($idCustomer);
        $customer->active = $isActive ? 1 : 0;
        $customer->update();
        $obj = new PrestaBtwoBRegistrationManageBtwoBCustomer();
        $obj->id_customer = $idCustomer;
        $obj->is_validated = $isActive ? 1 : 0;
        $obj->save();
        if ($isActive == 0) {
            if (isset(Context::getContext()->cookie)) {
                Context::getContext()->cookie->mylogout();
            }
            Cache::clean('getContextualValue_*');
            Tools::redirect(
                $this->context->link->getModuleLink(
                    $this->name,
                    'pendingaccount',
                    array(
                        'id_customer' => $customer->id,
                        'secure_key' => $customer->secure_key,
                        'new-account' => 1
                    )
                )
            );
        }
    }

    public function hookActionCustomerAccountUpdate($params)
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->enable_b2b) {
            $idCustomer = $this->context->customer->id;
            if ($registrationConfigure->enable_group_selection_one) {
                if ($registrationConfigure->enable_group_selection) {
                    $idCustomerGroup = Tools::getValue('customer_group');
                } else {
                    $idCustomerGroup = $registrationConfigure->assign_groups;
                }
                $helper = new PrestaBtwoBRegistrationHelper();
                $helper->addCustomerIntoDesireGroup($idCustomer, $idCustomerGroup);
            }
            $this->saveCustomerCustomFieldData($idCustomer);
        }
    }

    // This function is used to save data on registration form
    public function saveCustomerCustomFieldData($idCustomer)
    {
        $registrationFields = new PrestaBtwoBRegistrationCustomFields();
        $allFields = $registrationFields->getAllRegistrationFields();
        if ($allFields) {
            $customerRegistrationValue = new PrestaBtwoBRegistrationValue();
            $customerRegistrationValue->deleteCustomValues($idCustomer);
            foreach ($allFields as $field) {
                if ($field['field_type'] === PrestaBtwoBRegistrationHelper::MESSAGE) {
                    continue;
                }
                if ($field['field_type'] === PrestaBtwoBRegistrationHelper::FILE) {
                    $customId = 'presta_custom_' . $field['id_presta_btwob_custom_fields'];
                    if (!isset($_FILES[$customId]['name']) && Tools::getValue($customId)) {
                        $value = Tools::getValue($customId);
                    } else {
                        $value = $this->uploadLogo($_FILES, $field, $idCustomer);
                    }
                } else {
                    $customId = 'presta_custom_' . $field['id_presta_btwob_custom_fields'];
                    $value = Tools::getValue($customId);
                    if ($field['field_type'] === PrestaBtwoBRegistrationHelper::MULTISELECT ||
                        $field['field_type'] === PrestaBtwoBRegistrationHelper::CHECKBOX
                    ) {
                        $value = json_encode($value);
                    }
                }
                $cartValue = $this->context->cart;
                $customerRegistrationValue->id_shop_group = $cartValue->id_shop_group;
                $customerRegistrationValue->field_id = $field['id_presta_btwob_custom_fields'];
                $customerRegistrationValue->id_customer = $idCustomer;
                $customerRegistrationValue->field_value = $value;
                $customerRegistrationValue->add();
            }
        }
    }

    // This function is used to upload logo on frontent registration form.
    public function uploadLogo($file, $fieldInfo, $idCustomer)
    {
        if ($file && $fieldInfo && $idCustomer) {
            $customFile = $file['presta_custom_' . $fieldInfo['id_presta_btwob_custom_fields']];
            if ($customFile && $customFile['size'] > 0 && $customFile['tmp_name']) {
                $customerFolder = $this->absoluteImagePath . $idCustomer . '/';
                if (!is_dir($customerFolder)) {
                    @mkdir($customerFolder, 0777, true);
                    @chmod($customerFolder, 0777);
                }
                if ($fieldInfo['file_types']) {
                    $this->imageType = explode(',', $fieldInfo['file_types']);
                }
                $ext = pathinfo($customFile['name'], PATHINFO_EXTENSION);
                $imagename = str_replace(' ', '_', $customFile['name']);
                if (in_array($ext, $this->imageType)) {
                    if (ImageManager::isCorrectImageFileExt($imagename)) {
                        ImageManager::resize(
                            $customFile['tmp_name'],
                            $customerFolder . $imagename
                        );
                    } else {
                        move_uploaded_file($customFile['tmp_name'], $customerFolder . $imagename);
                    }
                }
                @chmod($customerFolder . $imagename, 0755);
                return $imagename;
            }
        }
    }

    public function hookDisplayAdminCustomers()
    {
        $idCustomer = Tools::getValue('id_customer');
        return $this->showCustomerInformation($idCustomer);

    }

    public function showCustomerInformation($idCustomer, $returnTPL = true)
    {
        $registrationFields = new PrestaBtwoBRegistrationValue();
        $customerFields = $registrationFields->getCustomFieldValues($idCustomer, $this->context->language->id);
        $fieldArray = array('checkbox', 'multiSelect');
        $fieldVal = array();
        if ($customerFields) {
            foreach ($customerFields as $key => $value) {
                if ($value['field_type'] == 'yes/no') {
                    $customerFields[$key]['field_value'] = $value['field_value'] == 1 ? $this->l('Yes') : $this->l('No');
                } elseif ($value['field_type'] == 'dropdown' || $value['field_type'] == 'radio') {
                    $customerFields[$key]['field_value'] = $registrationFields->getCustomerRegistrationDataValue(
                        $value['field_value']
                    );
                }
                elseif (in_array($value['field_type'], $fieldArray)) {
                    $fieldValues = json_decode($value['field_value']);
                    if ($fieldValues) {
                        foreach ($fieldValues as $fValue) {
                            $fieldVal[] = $registrationFields->getCustomerRegistrationDataValue($fValue);
                        }
                    }
                    $customerFields[$key]['field_value'] = implode(', ', $fieldVal);
                    unset($fieldVal);
                } else if ($value['field_type'] === PrestaBtwoBRegistrationHelper::FILE) {
                    $downloadLink = $this->context->link->getAdminLink(
                        'AdminPrestaAddBtwoBCustomFields',
                        true,
                        array(),
                        array(
                            'download' => 1,
                            'id' => $value['id_presta_btwob_registration_value'],
                            'id_customer' => $idCustomer
                        )
                    );
                    $customerFields[$key]['downloadLink'] = $downloadLink;
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'customerFields' => $customerFields
            )
        );
        if ($returnTPL) {
            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/presta_customer_info_backend.tpl'
            );
        }
    }

    public function hookValidateCustomerFormFields($params)
    {
        $currentPage = Tools::getValue('controller');
        $btobReg = (int) Tools::getValue('prestaBtoBRegistration');
        $allowedController = array('authentication', 'registration', 'identity');
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->address == 1 && $btobReg) {
            if (in_array($currentPage, $allowedController)) {
                $idCountry = (int) Tools::getValue('id_country');
                $vatNumber = Tools::getValue('vat_number');
                $country = new Country($idCountry);
                if (Country::getNeedZipCode($idCountry)) {
                    $zipCode = trim(Tools::getValue('postcode'));
                    foreach ($params['fields'] as $param) {
                        if (!$country->checkZipCode($zipCode) && $param->getName() == 'postcode') {
                            $param->addError(
                                sprintf(
                                    $this->l('Invalid postcode - should look like %s'),
                                    $country->zip_code_format
                                )
                            );
                        }
                    }
                }
                if ($registrationConfigure->vat_number && $registrationConfigure->vat_validation) {
                    $validIso = $this->getValidCountryForVies($country->iso_code);
                    if ($validIso && $vatNumber) {
                        foreach ($params['fields'] as $param) {
                            if ($param->getName() == 'vat_number') {
                                $client = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
                                $result = $client->checkVat(array(
                                    'countryCode' => $country->iso_code,
                                    'vatNumber' => $vatNumber
                                ));
                                if (!$result->valid) {
                                    $param->addError($this->l('Vat number is invalid.'));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->vat_number && $registrationConfigure->vat_validation) {
            $vatField = $params['form']->getField('vat_number');
            if ($vatField && $vatField->getValue()) {
                $idCountry = (int) Tools::getValue('id_country');
                $vatNumber = Tools::getValue('vat_number');
                $country = new Country($idCountry);
                $validIso = $this->getValidCountryForVies($country->iso_code);
                if ($validIso && $vatNumber) {
                    $client = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
                    $result = $client->checkVat(array(
                        'countryCode' => $country->iso_code,
                        'vatNumber' => $vatField->getValue()
                    ));
                    if (!$result->valid) {
                        $vatField->addError($this->l('Vat number is invalid.'));
                    }
                }
            }
        }
    }


    public function getValidCountryForVies($validCountryIsoCode)
    {
        $countries = array(
            'Austria' => 'AT',
            'Belgium' => 'BE',
            'Bulgaria' => 'BG',
            'Croatia' => 'HR',
            'Cyprus' => 'CY',
            'Czech Republic' => 'CZ',
            'Denmark' => 'DK',
            'Estonia' => 'EE',
            'Finland' => 'FI',
            'France' => 'FR',
            'Germany' => 'DE',
            'Greece' => 'EL',
            'Hungary' => 'HU',
            'Ireland' => 'IE',
            'Italy' => 'IT',
            'Latvia' => 'LV',
            'Lithuania' => 'LT',
            'Luxembourg' => 'LU',
            'Malta' => 'MT',
            'Netherlands' => 'NL',
            'Poland' => 'PL',
            'Portugal' => 'PT',
            'Romania' => 'RO',
            'Slovakia' => 'SK',
            'Slovenia' => 'SI',
            'Northern Ireland' => 'XI'
        );
        if (in_array($validCountryIsoCode, $countries)) {
            return true;
        }
        return false;
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        $currentPage = Tools::getValue('controller');
        $allowedController = array('authentication', 'registration');
        $registrationConfigure = $this->getModuleConfiguration();
        if (in_array($currentPage, $allowedController) &&
            (($registrationConfigure) && $registrationConfigure->address) == 1
        ) {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            } else {
                $availableCountries = Country::getCountries($this->context->language->id, true);
            }
            $formatter = new CustomerAddressFormatter(
                $this->context->country,
                $this->getTranslator(),
                $availableCountries
            );
            $addressFields = $formatter->getFormat();
            if (isset($addressFields['id_state'])) {
                $addressFields['id_state']->setRequired(false);
            }
            if ($addressFields) {
                if (isset($addressFields['firstname'])) {
                    unset($addressFields['firstname']);
                }
                if (isset($addressFields['lastname'])) {
                    unset($addressFields['lastname']);
                }
                if (isset($addressFields['alias'])) {
                    unset($addressFields['alias']);
                }
                if (isset($addressFields['company'])) {
                    unset($addressFields['company']);
                }
                if (isset($registrationConfigure) && $registrationConfigure->vat_number == 1) {
                    if (isset($addressFields['vat_number'])) {
                       ($addressFields['vat_number'])->setRequired(false);
                    }
                } else{
                    if (isset($addressFields['vat_number'])) {
                        unset($addressFields['vat_number']);
                    }
                }
                if (isset($addressFields['address1'])) {
                    $addressFields['address1']->setRequired(false);
                }
                if (isset($registrationConfigure) && $registrationConfigure->address_complement == 1) {
                    if (isset($addressFields['address2'])) {
                        ($addressFields['address2']);
                    }
                }else{
                    if (isset($addressFields['address2'])) {
                        unset($addressFields['address2']);
                    }
                }
                if (isset($registrationConfigure) && $registrationConfigure->phone == 1) {
                    if (isset($addressFields['phone'])) {
                       ($addressFields['phone']);
                    }
                }else{
                    if (isset($addressFields['phone'])) {
                        unset($addressFields['phone']);
                     }
                }
                if (isset($addressFields['city'])) {
                    ($addressFields['city'])->setRequired(false);
                }
                if (isset($addressFields['postcode'])) {
                    ($addressFields['postcode'])->setRequired(false);
                }
            }

            return $addressFields;
        }
    }

    public function callInstallTab()
    {
        $parentTab = 'AdminManageBtwoBRegistration';
        $this->installTab($parentTab, 'Manage B2B Registration', 'AdminParentCustomer');
        $this->installTab('AdminPrestaConfiguration','Configuration', $parentTab);
        $this->installTab('AdminPrestaManageBtwoBCustomer', 'Manage B2B Customer', $parentTab);
        $this->installTab('AdminPrestaAddBtwoBCustomFields', 'Add B2B Custom Fields', $parentTab);
        $this->installTab('AdminPrestaBtwoRelatedModules', 'Our Other Modules', $parentTab);
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;
        return $tab->add();
    }

    public function installTables()
    {
        $helper = new PrestaBtwoBRegistrationHelper();
        return $helper->createTable();
    }

    public function install()
    {
        if (!parent::install()
            || !$this->prestaHookRegister()
            || !$this->callInstallTab()
            || !$this->installTables()
        ) {
            return false;
        }
        return true;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $currentPage = Tools::getValue('controller');
        $allowedController = array('authentication', 'order', 'registration', 'identity', 'address');
        $registrationConfigure = $this->getModuleConfiguration();
        if (in_array($currentPage, $allowedController) && $registrationConfigure->enable_b2b) {
            $this->context->controller->registerStylesheet(
                'prestabtwobregistration_prestabtwobregistration_css',
                'modules/' . $this->name . '/views/css/front/customer_form_top.css',
                array(
                    'priority' => 999,
                )
            );
            $this->context->controller->registerJavaScript(
                'prestabtwobregistration_prestabtwobregistration_js',
                'modules/' . $this->name . '/views/js/front/presta_display_customer_top.js',
                array(
                    'position' => 'bottom',
                    'priority' => 999
                )
            );
        }
    }

    public function hookDisplayNav2()
    {
        $registrationConfigure = $this->getModuleConfiguration();
        if ($registrationConfigure->enable_b2b && $registrationConfigure->top_link_text) {
            if (_PS_VERSION_ <  8.0) {
                $presta_lkink =  $this->context->link->getPageLink(
                    'authentication',
                    true,
                    null,
                    array(
                        'create_account' => 1
                    )
                );
            } else {
                $presta_lkink = $this->context->link->getPageLink('registration', true);
            }
            $this->context->smarty->assign(
                array(
                    'presta_topl_link_label' => $registrationConfigure->top_link_text,
                    'presta_btwob_top_link' => $presta_lkink
                )
            );
            return $this->display(__FILE__,'display_nav_2.tpl');
        }
    }

    public function prestaHookRegister()
    {
        return $this->registerHook(
            array(
                'displayCustomerAccountFormTop',
                'displayHeader',
                'actionFrontControllerSetMedia',
                'displayCustomerAccountForm',
                'displayPersonalInformationTop',
                'actionSubmitAccountBefore',
                'actionCustomerAccountAdd',
                'displayAdminCustomers',
                'actionCustomerAccountUpdate',
                'additionalCustomerFormFields',
                'validateCustomerFormFields',
                'actionValidateCustomerAddressForm',
                'displayNav2'
            )
        );
    }

    public function uninstall()
    {
        Configuration::updateValue('PS_B2B_ENABLE', 0);
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->dropTable()
            || !$this->removeImageFolder()
        ) {
            return false;
        }
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }
        return true;
    }

    public function dropTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
                `' . _DB_PREFIX_ . 'presta_btwob_registration_configuration_lang`,
                `' . _DB_PREFIX_ . 'presta_btwob_registration_configuration`,
                `' . _DB_PREFIX_ . 'presta_manage_btwob_customer`,
                `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`,
                `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_lang`,
                `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value`,
                `' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value_lang`,
                `' . _DB_PREFIX_ . 'presta_btwob_registration_value`'
        );
    }

    public function removeImageFolder()
    {
        if ($this->absoluteImagePath) {
        $ticket = glob($this->absoluteImagePath . '/*');
            foreach ($ticket as $folder) {
                $images = glob($folder . '/*');
                if (is_dir($folder)) {
                    foreach ($images as $image) {
                        unlink($image);
                    }
                    rmdir($folder);
                }
            }
        }
        return true;
    }
}
