<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_ClassLoader.php';

/**
 * Class AdminTNTOfficielAccountController
 */
class AdminTNTOfficielAccountController extends ModuleAdminController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        TNTOfficiel_Logstack::log();

        // Bootstrap enable.
        $this->bootstrap = true;

        parent::__construct();

        $this->page_header_toolbar_title = sprintf(
            TNTOfficiel::getCodeTranslate('titleHeaderConfigureStr'),
            TNTOfficiel::MODULE_TITLE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createTemplate($tpl_name)
    {
        TNTOfficiel_Logstack::log();

        if (file_exists($this->getTemplatePath() . $tpl_name) && $this->viewAccess()) {
            return $this->context->smarty->createTemplate($this->getTemplatePath() . $tpl_name, $this->context->smarty);
        }

        return parent::createTemplate($tpl_name);
    }

    /**
     * Load script.
     */
    public function setMedia($isNewTheme = false)
    {
        TNTOfficiel_Logstack::log();

        parent::setMedia(false);

        $this->module->addJS('AdminTNTOfficielAccount.js');
    }

    /**
     * Display page.
     */
    public function renderList()
    {
        TNTOfficiel_Logstack::log();

        parent::renderList();

        $arrWeightUnitList = TNTOfficielCarrier::getWeightUnitList();
        $strShopWeightUnit = TNTOfficielCarrier::getShopWeightUnit();

        // Display warning message in the module list for weight unit.
        if (!array_key_exists($strShopWeightUnit, $arrWeightUnitList)) {
            $this->warnings[] = sprintf(
                TNTOfficiel::getCodeTranslate('warnWeightUnitStr'),
                implode('/', array_keys($arrWeightUnitList)),
                $strShopWeightUnit
            );
        }

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return false;
        }

        // Form Helper.
        $objHelperForm = new HelperForm();

        // Form Structure used as parameter for Helper 'generateForm' method.
        $arrFormStruct = array();
        // Form Values used for Helper 'fields_value' property.
        $arrFieldsValue = array();

        //$objHelperForm->base_folder = 'helpers/form/';
        $objHelperForm->base_tpl = 'AdminTNTOfficielAccount.tpl';

        // Module using this form.
        $objHelperForm->module = $this->module;
        // Controller name.
        $objHelperForm->name_controller = TNTOfficiel::MODULE_NAME;
        // Token.
        $objHelperForm->token = Tools::getAdminTokenLite('AdminTNTOfficielAccount');
        // Form action attribute.
        $objHelperForm->currentIndex = AdminController::$currentIndex . '&configure=' . TNTOfficiel::MODULE_NAME;

        // Language.
        $objHelperForm->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $objHelperForm->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        // Smarty assign().
        // /modules/<MODULE>/views/templates/admin/_configure/helpers/form/form.tpl
        // extends /<ADMIN>/themes/default/template/helpers/form/form.tpl
        $objHelperForm->tpl_vars['tntofficiel'] = array(
            'srcTNTLogoImage' => TNTOfficiel::getURLModulePath('image') . 'logo/500x100.png',
            'hrefExportLog' => $this->context->link->getAdminLink(
                'AdminTNTOfficielOrders',
                true,
                array(),
                array(
                    'action' => 'downloadLogs',
                )
            ),
            'langExportLog' => TNTOfficiel::getCodeTranslate('buttonExportLogStr'),
            'hrefManualPDF' => 'https://www.tnt.fr/Telechargements/cit/manuel-prestashop-1.7.pdf',
            // TNTOfficiel::getURLModulePath().'manuel-prestashop.pdf',
            'langManualPDF' => TNTOfficiel::getCodeTranslate('buttonInstallationManualStr'),
        );

        /*
         * Configuration Form
         */

        $arrFormInputAccountConfig = array();
        $arrAllMessageList = array();

        $arrFormHR = array(
            array(
                'type' => 'html',
                'name' => 'html_data',
                'html_content' => '<div class="col-md-12"><hr /></div>',
            ),
        );

        $strIDFormAccountConfig = 'submit' . TNTOfficiel::MODULE_NAME . 'AccountConfig';

        // Previous auth state.
        $objWasValidatedDateTime = $objTNTContextAccountModel->getAuthValidatedDateTime();
        // Form auth. Validate Auth.
        $arrFormAuth = $this->getFormAccountAuth($strIDFormAccountConfig, $arrFieldsValue);
        // New auth state.
        $objNowValidatedDateTime = $objTNTContextAccountModel->getAuthValidatedDateTime();

        // If submit and auth change to validated, do not POST empty field for the displayed section.
        if (Tools::getIsset($strIDFormAccountConfig)
            && $objWasValidatedDateTime === null && $objNowValidatedDateTime !== null
        ) {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminTNTOfficielAccount')
            );
            exit;
        }

        $arrFormInputAccountConfig = array_merge($arrFormInputAccountConfig, $arrFormAuth['input']);
        $arrAllMessageList[] = $arrFormAuth['message'];

        if ($objNowValidatedDateTime !== null) {
            // Form sender.
            $arrFormSender = $this->getFormAccountSender($strIDFormAccountConfig, $arrFieldsValue);
            // Form Pickup.
            $arrFormPickup = $this->getFormAccountPickup($strIDFormAccountConfig, $arrFieldsValue);
            // Form Zone.
            $arrFormZone = $this->getFormAccountZone($strIDFormAccountConfig, $arrFieldsValue);
            // Form OrderState.
            $arrFormOrderState = $this->getFormAccountOrderState($strIDFormAccountConfig, $arrFieldsValue);

            $arrFormInputAccountConfig = array_merge(
                $arrFormInputAccountConfig,
                $arrFormHR,
                $arrFormSender['input'],
                $arrFormHR,
                $arrFormPickup['input'],
                $arrFormHR,
                $arrFormZone['input'],
                $arrFormHR,
                $arrFormOrderState['input']
            );

            $arrAllMessageList[] = $arrFormSender['message'];
            $arrAllMessageList[] = $arrFormPickup['message'];
            $arrAllMessageList[] = $arrFormZone['message'];
            $arrAllMessageList[] = $arrFormOrderState['message'];
        }

        if (Shop::getContext() === Shop::CONTEXT_SHOP) {
            $arrFormInputContext = array(
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<input type="hidden" name="AdminConfigContextShop" value="1" />',
                ),
            );

            $arrFormInputAccountConfig = array_merge(
                $arrFormInputAccountConfig,
                $arrFormInputContext
            );
        }

        $arrFormRequiredAccountConfig = array();
        $arrFormRequiredAccountConfigLabel = array();
        if (Tools::getIsset($strIDFormAccountConfig)) {
            foreach ($arrFormInputAccountConfig as $arrField) {
                if (array_key_exists('required', $arrField) && $arrField['required'] === true) {
                    if (array_key_exists($arrField['name'], $arrFieldsValue)
                        && $arrFieldsValue[$arrField['name']] === ''
                    ) {
                        // Red highlight if error on field.
                        $arrFormRequiredAccountConfig[] = $arrField['name'];
                        // List required fields names for error message.
                        $arrFormRequiredAccountConfigLabel[] = $arrField['label'];
                    }
                }
            }

            foreach ($arrAllMessageList as $arrErrorCopy) {
                foreach ($arrErrorCopy['error'] as $mxdFieldName => $strErrorMsg) {
                    if (is_string($mxdFieldName)) {
                        // Red highlight if error on field.
                        $arrFormRequiredAccountConfig[] = $mxdFieldName;
                    }
                }
            }
        }

        $objHelperForm->tpl_vars['tntofficiel'] += array(
            'errorFields' => $arrFormRequiredAccountConfig,
        );

        /*
         * Merge messages.
         */

        $arrFormMessageAccountConfig = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        if (Tools::getIsset($strIDFormAccountConfig)) {
            foreach ($arrFormMessageAccountConfig as $strType => $arrValue) {
                foreach ($arrAllMessageList as $arrAlertCopy) {
                    if (array_key_exists($strType, $arrAlertCopy)) {
                        foreach ($arrAlertCopy[$strType] as $mxdFieldName => $strValueCopy) {
                            if (is_string($mxdFieldName)) {
                                $arrFormMessageAccountConfig[$strType][$mxdFieldName] = $strValueCopy;
                            } else {
                                $arrFormMessageAccountConfig[$strType][] = $strValueCopy;
                            }
                        }
                    }
                }
            }

            // Add required message.
            if (count($arrFormRequiredAccountConfigLabel) === 1) {
                $arrFormMessageAccountConfig['error'][$arrFormRequiredAccountConfig[0]] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorFieldMandatoryPleaseCheckStr'),
                    implode(', ', $arrFormRequiredAccountConfigLabel)
                );
            } elseif (count($arrFormRequiredAccountConfigLabel) > 1) {
                $arrFormMessageAccountConfig['error'][] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorFieldsMandatoryPleaseCheckStr'),
                    implode(', ', $arrFormRequiredAccountConfigLabel)
                );
            }
        }

        if (count($arrFormMessageAccountConfig['error']) > 0) {
            // If error, do not display success.
            unset($arrFormMessageAccountConfig['success']);
        } elseif (count($arrFormMessageAccountConfig['success']) > 0) {
            // Use only one common success message.
            $arrFormMessageAccountConfig['success'] = array(
                TNTOfficiel::getCodeTranslate('successSettingsUpdatedStr'),
            );
        }

        $arrFormMessageAccountConfig['info'][] = TNTOfficiel::getCodeTranslate('infoSelectContextStr');

        $arrHRAZipcodeList = TNTOfficielCarrier::getHRAZipCodeList();
        if (empty($arrHRAZipcodeList)) {
            $arrFormMessageAccountConfig['warning'][] = TNTOfficiel::getCodeTranslate('warnFileZoneNotFoundStr');
        }

        // HTML Formatting.
        $arrFormAccountConfigMessageInput = array();
        $arrFormAccountConfigMessageHTML = TNTOfficiel_Tools::getAlertHTML($arrFormMessageAccountConfig);
        if (count($arrFormAccountConfigMessageHTML) > 0) {
            $arrFormAccountConfigMessageInput = array(
                array(
                    'type' => 'html',
                    'name' => implode('', $arrFormAccountConfigMessageHTML),
                ),
            );
        }

        // Add form.
        $arrFormStruct[$strIDFormAccountConfig] = array(
            'form' => array(
                'legend' => array(
                    'title' => TNTOfficiel::getCodeTranslate('titleFormMerchantStr'),
                ),
                'input' => array_merge($arrFormAccountConfigMessageInput, $arrFormInputAccountConfig),
                'submit' => array(
                    'title' => TNTOfficiel::getCodeTranslate('buttonSaveStr'),
                    'class' => 'btn btn-default pull-right',
                    'name' => $strIDFormAccountConfig,
                ),
            ),
        );

        // Set all form fields values.
        $objHelperForm->fields_value = $arrFieldsValue;

        // Global Submit ID.
        //$objHelperForm->submit_action = 'submit'.TNTOfficiel::MODULE_NAME;
        // Get generated forms.
        $strDisplayForms = $objHelperForm->generateForm($arrFormStruct);

        $this->content = $strDisplayForms;

        /*
         * HRA (Footer)
         */

        return '';
    }

    /**
     * Get the Account Auth form data for Helper.
     *
     * @return array
     */
    private function getFormAccountAuth($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesAuth = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array('input' => array(), 'message' => $arrFormMessagesAuth);
        }

        // Input values.
        // ObjectModel self escape data with formatValue(). Do not double escape using pSQL.
        $strArgAccountNumber = trim((string)Tools::getValue('TNTOFFICIEL_ACCOUNT_NUMBER'));
        $strArgAccountLogin = trim((string)Tools::getValue('TNTOFFICIEL_ACCOUNT_LOGIN'));
        $strArgAccountPassword = (string)Tools::getValue('TNTOFFICIEL_ACCOUNT_PASSWORD');

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_ACCOUNT_NUMBER'] = $objTNTContextAccountModel->account_number;
        $arrRefFieldsValue['TNTOFFICIEL_ACCOUNT_LOGIN'] = $objTNTContextAccountModel->account_login;
        $arrRefFieldsValue['TNTOFFICIEL_ACCOUNT_PASSWORD'] = TNTOfficielAccount::PASSWORD_REPLACE;
        if (Tools::getIsset($strArgIDForm)) {
            $arrRefFieldsValue['TNTOFFICIEL_ACCOUNT_NUMBER'] = $strArgAccountNumber;
            $arrRefFieldsValue['TNTOFFICIEL_ACCOUNT_LOGIN'] = $strArgAccountLogin;
        }

        // If store request (else simple check).
        if (Tools::getIsset($strArgIDForm)
            // and account invalid or change are made in form.
            && ($objTNTContextAccountModel->getAuthValidatedDateTime() === null
                || ($objTNTContextAccountModel->account_number !== $strArgAccountNumber
                    || $objTNTContextAccountModel->account_login !== $strArgAccountLogin
                    || ($objTNTContextAccountModel->getAccountPassword() !== sha1($strArgAccountPassword)
                        && $strArgAccountPassword !== TNTOfficielAccount::PASSWORD_REPLACE
                    )
                )
            )
        ) {
            /*
             * Save
             */

            // If not empty, save account for validation request.
            if ($strArgAccountNumber && $strArgAccountLogin && $strArgAccountPassword) {
                $objTNTContextAccountModel->setAccountLogin($strArgAccountLogin);
                $objTNTContextAccountModel->setAccountNumber($strArgAccountNumber);
                if ($strArgAccountPassword !== TNTOfficielAccount::PASSWORD_REPLACE) {
                    $objTNTContextAccountModel->setAccountPassword($strArgAccountPassword);
                }
                // Validate the TNT credentials.
                $mxdStateValidation = $objTNTContextAccountModel->updateAuthValidation();
                if ($mxdStateValidation === null) {
                    $arrFormMessagesAuth['warning'][] = TNTOfficiel::getCodeTranslate('errorConnection');
                } elseif (!$mxdStateValidation) {
                    $arrFormMessagesAuth['error'][] = TNTOfficiel::getCodeTranslate('errorUnrecognizedAccountStr');
                } else {
                    // Save and also for each shop in account context.
                    $objTNTContextAccountModel->saveContextShop();
                    $arrFormMessagesAuth['success'][] = TNTOfficiel::getCodeTranslate('successAccountUpdatedStr');
                }
            }
        } else {
            $mxdStateValidation = $objTNTContextAccountModel->updateAuthValidation();
            if ($mxdStateValidation === null) {
                $arrFormMessagesAuth['warning'][] = TNTOfficiel::getCodeTranslate('errorConnection');
            } elseif (!$mxdStateValidation) {
                $arrFormMessagesAuth['error'][] = TNTOfficiel::getCodeTranslate('errorUnrecognizedAccountStr');
            }
        }

        $arrShopName = array();
        $arrObjPSShopList = $objTNTContextAccountModel->getPSShopList();
        foreach ($arrObjPSShopList as $intShopID => $objPSShop) {
            $arrShopName[] = $objPSShop->name;
        }
        $strShopList = implode(', ', $arrShopName);
        $strHTMLShops = '<b>' . $strShopList . '</b>';

        return array(
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<div class="alert alert-context">'
                        . sprintf(TNTOfficiel::getCodeTranslate('alertSettingModifiedOnStr'), $strHTMLShops) . '.'
                        . '</div>',
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelAccountNumStr'),
                    'name' => 'TNTOFFICIEL_ACCOUNT_NUMBER',
                    'maxlength' => 8,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelLoginStr'),
                    'name' => 'TNTOFFICIEL_ACCOUNT_LOGIN',
                    'maxlength' => 128,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'password',
                    'label' => TNTOfficiel::getCodeTranslate('labelPasswordStr'),
                    'name' => 'TNTOFFICIEL_ACCOUNT_PASSWORD',
                    'size' => 6,
                    'required' => true,
                ),
            ),
            'message' => $arrFormMessagesAuth,
        );
    }

    /**
     * Get the Account Sender form data for Helper.
     *
     * @return array
     */
    private function getFormAccountSender($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesSender = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array();
        }

        // Input values.
        // ObjectModel self escape data with formatValue(). Do not double escape using pSQL.
        $strSenderCompany = TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_SOCIETE'), 32);
        $strSenderAddress1 = TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_ADRESSE_1'), 32);
        $strSenderAddress2 = TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_ADRESSE_2'), 32);
        $strSenderZipCode = trim((string)Tools::getValue('TNTOFFICIEL_CODE_POSTAL'));
        $strSenderCity = trim((string)Tools::getValue('TNTOFFICIEL_VILLE'));
        $strSenderFirstName = TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_PRENOM'), 20);
        $strSenderLastName = TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_NOM'), 20);
        $strSenderEmail = Tools::strtolower(
            TNTOfficiel_Tools::translitASCII((string)Tools::getValue('TNTOFFICIEL_MAIL'), 80)
        );
        $strSenderPhone = trim((string)Tools::getValue('TNTOFFICIEL_TELEPHONE'));

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_SOCIETE'] = $objTNTContextAccountModel->sender_company;
        $arrRefFieldsValue['TNTOFFICIEL_ADRESSE_1'] = $objTNTContextAccountModel->sender_address1;
        $arrRefFieldsValue['TNTOFFICIEL_ADRESSE_2'] = $objTNTContextAccountModel->sender_address2;
        $arrRefFieldsValue['TNTOFFICIEL_CODE_POSTAL'] = $objTNTContextAccountModel->sender_zipcode;
        $arrRefFieldsValue['TNTOFFICIEL_VILLE'] = $objTNTContextAccountModel->sender_city;
        $arrRefFieldsValue['TNTOFFICIEL_PRENOM'] = $objTNTContextAccountModel->sender_firstname;
        $arrRefFieldsValue['TNTOFFICIEL_NOM'] = $objTNTContextAccountModel->sender_lastname;
        $arrRefFieldsValue['TNTOFFICIEL_MAIL'] = $objTNTContextAccountModel->sender_email;
        $arrRefFieldsValue['TNTOFFICIEL_TELEPHONE'] = $objTNTContextAccountModel->sender_phone;
        if (Tools::getIsset($strArgIDForm)) {
            $arrRefFieldsValue['TNTOFFICIEL_SOCIETE'] = $strSenderCompany;
            $arrRefFieldsValue['TNTOFFICIEL_ADRESSE_1'] = $strSenderAddress1;
            $arrRefFieldsValue['TNTOFFICIEL_ADRESSE_2'] = $strSenderAddress2;
            $arrRefFieldsValue['TNTOFFICIEL_CODE_POSTAL'] = $strSenderZipCode;
            $arrRefFieldsValue['TNTOFFICIEL_VILLE'] = $strSenderCity;
            $arrRefFieldsValue['TNTOFFICIEL_PRENOM'] = $strSenderFirstName;
            $arrRefFieldsValue['TNTOFFICIEL_NOM'] = $strSenderLastName;
            $arrRefFieldsValue['TNTOFFICIEL_MAIL'] = $strSenderEmail;
            $arrRefFieldsValue['TNTOFFICIEL_TELEPHONE'] = $strSenderPhone;
        }

        /*
         * Validate the Sender and return error messages.
         */

        $strSenderCountryISO = 'FR';

        $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide(
            $strSenderCountryISO,
            $arrRefFieldsValue['TNTOFFICIEL_CODE_POSTAL'],
            $arrRefFieldsValue['TNTOFFICIEL_VILLE']
        );

        // If store request (else simple check).
        if (Tools::getIsset($strArgIDForm)) {
            $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide(
                $strSenderCountryISO,
                $strSenderZipCode,
                $strSenderCity
            );

            // Unsupported country or communication error is considered true to prevent
            // always invalid address form and show error "unknow postcode" on Front-Office checkout.
            $boolPostCodeIsValid = (!$arrResultCitiesGuide['boolIsCountrySupported']
                || $arrResultCitiesGuide['boolIsRequestComError']
                || count($arrResultCitiesGuide['arrCitiesNameList']) > 0
            );
            $boolCityIsValid = (!$arrResultCitiesGuide['boolIsCountrySupported']
                || $arrResultCitiesGuide['boolIsRequestComError']
                || $arrResultCitiesGuide['boolIsCityNameValid']
            );
            if ($strSenderZipCode && !$boolPostCodeIsValid) {
                $arrFormMessagesSender['error']['TNTOFFICIEL_CODE_POSTAL'] =
                    TNTOfficiel::getCodeTranslate('errorInvalidPostalCodeStr');
            }
            if ($strSenderCity && !$boolCityIsValid) {
                $arrFormMessagesSender['error']['TNTOFFICIEL_VILLE'] =
                    TNTOfficiel::getCodeTranslate('errorInvalidCityStr');
            }
            if ($strSenderZipCode && $boolPostCodeIsValid) {
                $arrRefFieldsValue['TNTOFFICIEL_CODE_POSTAL'] = $arrResultCitiesGuide['strZipCode'];
            }
            if ($strSenderCity && $boolCityIsValid) {
                $arrRefFieldsValue['TNTOFFICIEL_VILLE'] = $arrResultCitiesGuide['strCity'];
            }

            // Auto formatting.
            $strSenderZipCode = $arrResultCitiesGuide['strZipCode'];
            $strSenderCity = $arrResultCitiesGuide['strCity'];

            if (!Validate::isEmail($strSenderEmail)) {
                $arrFormMessagesSender['error']['TNTOFFICIEL_MAIL'] =
                    TNTOfficiel::getCodeTranslate('errorInvalidEmailStr');
            }

            $strSenderMobilePhone = TNTOfficiel_Tools::validateMobilePhone($strSenderCountryISO, $strSenderPhone);
            $strSenderFixedPhone = TNTOfficiel_Tools::validateFixedPhone($strSenderCountryISO, $strSenderPhone);
            // Cleaned Phone.
            $strSenderPhone = ($strSenderMobilePhone !== false ? $strSenderMobilePhone : $strSenderFixedPhone);

            if ($strSenderPhone === false) {
                $arrFormMessagesSender['error']['TNTOFFICIEL_TELEPHONE'] =
                    TNTOfficiel::getCodeTranslate('errorInvalidPhoneStr');
            } else {
                $arrRefFieldsValue['TNTOFFICIEL_TELEPHONE'] = $strSenderPhone;
            }

            /*
             * Save
             */

            // If no errors.
            if (count($arrFormMessagesSender['error']) === 0) {
                $objTNTContextAccountModel->setSenderCompany($strSenderCompany);
                $objTNTContextAccountModel->setSenderAddress1($strSenderAddress1);
                $objTNTContextAccountModel->setSenderAddress2($strSenderAddress2);
                $objTNTContextAccountModel->setSenderZipCode($strSenderZipCode);
                $objTNTContextAccountModel->setSenderCity($strSenderCity);
                $objTNTContextAccountModel->setSenderFirstName($strSenderFirstName);
                $objTNTContextAccountModel->setSenderLastName($strSenderLastName);
                $objTNTContextAccountModel->setSenderEMail($strSenderEmail);
                $objTNTContextAccountModel->setSenderPhone($strSenderPhone);
                // Save and also for each shop in account context.
                $objTNTContextAccountModel->saveContextShop();

                $arrFormMessagesSender['success'][] = TNTOfficiel::getCodeTranslate('successSenderUpdatedStr');
            }
        }

        $objAllZipCodeCities = array();
        foreach ($arrResultCitiesGuide['arrCitiesNameList'] as $strCities) {
            $objAllZipCodeCities[] = (object)array(
                'name' => $strCities,
                'id' => $strCities,
            );
        }

        return array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelCompanyStr'),
                    'name' => 'TNTOFFICIEL_SOCIETE',
                    'maxlength' => 32,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelAddressStr'),
                    'name' => 'TNTOFFICIEL_ADRESSE_1',
                    'maxlength' => 32,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelAddressSupplementStr'),
                    'name' => 'TNTOFFICIEL_ADRESSE_2',
                    'maxlength' => 32,
                    'size' => 6,
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelZipCodeStr'),
                    'name' => 'TNTOFFICIEL_CODE_POSTAL',
                    'maxlength' => 10,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelCityStr'),
                    'name' => 'TNTOFFICIEL_VILLE',
                    'maxlength' => 32,
                    'required' => true,
                    //'class' => 'col-md-6',
                    'options' => array(
                        'query' => $objAllZipCodeCities,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelFirstNameStr'),
                    'name' => 'TNTOFFICIEL_PRENOM',
                    'maxlength' => 20,
                    'size' => 6,
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelLastNameStr'),
                    'name' => 'TNTOFFICIEL_NOM',
                    'maxlength' => 20,
                    'size' => 6,
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelEmailStr'),
                    'name' => 'TNTOFFICIEL_MAIL',
                    'maxlength' => 80,
                    'size' => 6,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelPhoneStr'),
                    'name' => 'TNTOFFICIEL_TELEPHONE',
                    'maxlength' => 32,
                    'size' => 6,
                    'required' => true,
                ),
            ),
            'message' => $arrFormMessagesSender,
        );
    }

    /**
     * Get the Account Pickup form data for Helper.
     *
     * @return array
     */
    private function getFormAccountPickup($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesPickup = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array();
        }

        $arrHeureDriver = array();
        $arrMinuteDriver = array();
        $arrHeureClosing = array();
        $arrMinuteClosing = array();

        for ($i = 8; $i < 23; $i++) {
            $value = Tools::strlen($i) < 2 ? '0' . $i : $i;
            array_push(
                $arrHeureDriver,
                array(
                    'idheure' => $i,
                    'name' => $value,
                )
            );
        }

        for ($j = 0; $j < 60; $j++) {
            $valueMinute = Tools::strlen($j) < 2 ? '0' . $j : $j;
            array_push(
                $arrMinuteDriver,
                array(
                    'idminute' => $j,
                    'name' => $valueMinute,
                )
            );
        }

        for ($i = 15; $i < 24; $i++) {
            $value = Tools::strlen($i) < 2 ? '0' . $i : $i;
            array_push(
                $arrHeureClosing,
                array(
                    'idheure' => $i,
                    'name' => $value,
                )
            );
        }

        for ($j = 0; $j < 60; $j++) {
            $valueMinute = Tools::strlen($j) < 2 ? '0' . $j : $j;
            array_push(
                $arrMinuteClosing,
                array(
                    'idminute' => $j,
                    'name' => $valueMinute,
                )
            );
        }

        // Input values.
        // ObjectModel self escape data with formatValue(). Do not double escape using pSQL.
        $strPickupLabelType = (string)Tools::getValue('TNTOFFICIEL_ETIQUETTE');
        $strPreparationDays = trim((string)Tools::getValue('TNTOFFICIEL_DELAI_PREPARATION'));
        $strDeliveryNotification = (string)Tools::getValue('TNTOFFICIEL_NOTIFICATION');
        $strDeliveryInsurance = (string)Tools::getValue('TNTOFFICIEL_INSURANCE');
        $strDisplayEDD = (string)Tools::getValue('TNTOFFICIEL_DATE_PREVISIONNELLE');

        $strPickupType = (string)Tools::getValue('TNTOFFICIEL_TYPE_RAMASSAGE');
        $strPickupHourDriver = (string)Tools::getValue('TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER');
        $strPickupMinuteDriver = sprintf("%02s", (string)Tools::getValue('TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER'));
        $strPickupHourClosing = (string)Tools::getValue('TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING');
        $strPickupMinuteClosing = sprintf("%02s", (string)Tools::getValue('TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING'));
        $strPickupDisplayNumber = (string)Tools::getValue('TNTOFFICIEL_AFFICHAGE_RAMASSAGE');
        $strAPIGoogleMapKey = trim((string)Tools::getValue('TNTOFFICIEL_GMAP_API_KEY'));

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_ETIQUETTE'] = $objTNTContextAccountModel->pickup_label_type;
        $arrRefFieldsValue['TNTOFFICIEL_DELAI_PREPARATION'] = ($objTNTContextAccountModel->pickup_preparation_days ?
            $objTNTContextAccountModel->pickup_preparation_days :
            '0'
        );
        $arrRefFieldsValue['TNTOFFICIEL_NOTIFICATION'] = $objTNTContextAccountModel->delivery_notification;
        $arrRefFieldsValue['TNTOFFICIEL_INSURANCE'] = $objTNTContextAccountModel->delivery_insurance;
        $arrRefFieldsValue['TNTOFFICIEL_DATE_PREVISIONNELLE'] = $objTNTContextAccountModel->delivery_display_edd;

        $arrRefFieldsValue['TNTOFFICIEL_TYPE_RAMASSAGE'] = $objTNTContextAccountModel->pickup_type;

        $arrRefFieldsValue['TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER'] =
            $objTNTContextAccountModel->getPickupDriverTime()->format('H');
        $arrRefFieldsValue['TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER'] =
            $objTNTContextAccountModel->getPickupDriverTime()->format('i');
        $arrRefFieldsValue['TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING'] =
            $objTNTContextAccountModel->getPickupClosingTime()->format('H');
        $arrRefFieldsValue['TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING'] =
            $objTNTContextAccountModel->getPickupClosingTime()->format('i');

        $arrRefFieldsValue['TNTOFFICIEL_AFFICHAGE_RAMASSAGE'] = $objTNTContextAccountModel->pickup_display_number;
        $arrRefFieldsValue['TNTOFFICIEL_GMAP_API_KEY'] = $objTNTContextAccountModel->api_google_map_key;

        /*
         * Validate the Pickup and return error messages.
         */

        // If store request (else simple check).
        if (Tools::getIsset($strArgIDForm)) {
            $arrRefFieldsValue['TNTOFFICIEL_ETIQUETTE'] = $strPickupLabelType;
            $arrRefFieldsValue['TNTOFFICIEL_DELAI_PREPARATION'] = $strPreparationDays;
            $arrRefFieldsValue['TNTOFFICIEL_NOTIFICATION'] = $strDeliveryNotification;
            $arrRefFieldsValue['TNTOFFICIEL_INSURANCE'] = $strDeliveryInsurance;
            $arrRefFieldsValue['TNTOFFICIEL_DATE_PREVISIONNELLE'] = $strDisplayEDD;
            $arrRefFieldsValue['TNTOFFICIEL_TYPE_RAMASSAGE'] = Tools::getValue('TNTOFFICIEL_TYPE_RAMASSAGE');
            $arrRefFieldsValue['TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER'] =
                Tools::getValue('TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER');
            $arrRefFieldsValue['TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER'] =
                Tools::getValue('TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER');
            $arrRefFieldsValue['TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING'] =
                Tools::getValue('TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING');
            $arrRefFieldsValue['TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING'] =
                Tools::getValue('TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING');
            $arrRefFieldsValue['TNTOFFICIEL_AFFICHAGE_RAMASSAGE'] = $strPickupDisplayNumber;
            $arrRefFieldsValue['TNTOFFICIEL_GMAP_API_KEY'] = $strAPIGoogleMapKey;

            // Check.
            if ($strPreparationDays !== (string)(int)$strPreparationDays
                || (int)$strPreparationDays < 0
                || (int)$strPreparationDays > 30
            ) {
                $arrFormMessagesPickup['error']['TNTOFFICIEL_DELAI_PREPARATION'] =
                    TNTOfficiel::getCodeTranslate('errorPreparationTimeStr');
            }

            /*
             * Save
             */

            // If no errors.
            if (count($arrFormMessagesPickup['error']) === 0) {
                $objTNTContextAccountModel->setPickupLabelType($strPickupLabelType);
                $objTNTContextAccountModel->pickup_preparation_days = $strPreparationDays;
                $objTNTContextAccountModel->delivery_notification = $strDeliveryNotification;
                $objTNTContextAccountModel->delivery_insurance = $strDeliveryInsurance;
                $objTNTContextAccountModel->delivery_display_edd = $strDisplayEDD;

                $objTNTContextAccountModel->setPickupType($strPickupType);

                $objTNTContextAccountModel->pickup_driver_time =
                    TNTOfficiel_Tools::getDateTime($strPickupHourDriver . ':' . $strPickupMinuteDriver . ':00')
                        ->format('H:i');
                $objTNTContextAccountModel->pickup_closing_time =
                    TNTOfficiel_Tools::getDateTime($strPickupHourClosing . ':' . $strPickupMinuteClosing . ':00')
                        ->format('H:i');

                $objTNTContextAccountModel->pickup_display_number = $strPickupDisplayNumber;
                $objTNTContextAccountModel->api_google_map_key = $strAPIGoogleMapKey;

                // Save and also for each shop in account context.
                $objTNTContextAccountModel->saveContextShop();

                $arrFormMessagesPickup['success'][] = TNTOfficiel::getCodeTranslate('successSettingsUpdatedStr');
            }
        }

        $strAPIKGMDesc = TNTOfficiel::getCodeTranslate('infoAPIKeyGMapServiceStr') . ' '
            . TNTOfficiel::getCodeTranslate('infoAPIKeyGMapAccountStr')
            . '<br />'
            . sprintf(
                TNTOfficiel::getCodeTranslate('infoAPIKeyGMapMoreInfoStr'),
                '<a target="_blank"
                    href="https://console.developers.google.com/apis/credentials/wizard?api=maps_backend"
                 >',
                '</a>'
            );

        return array(
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelPrintFormatStr'),
                    'name' => 'TNTOFFICIEL_ETIQUETTE',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'STDA4',
                                'name' => TNTOfficiel::getCodeTranslate('optionPrintA4Str'),
                            ),
                            array(
                                'id' => 'THERMAL',
                                'name' => TNTOfficiel::getCodeTranslate('optionPrintThermalStr'),
                            ),
                            array(
                                'id' => 'THERMAL,NO_LOGO',
                                'name' => TNTOfficiel::getCodeTranslate('optionPrintThermalLogoStr'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelPreparationTimeStr'),
                    'name' => 'TNTOFFICIEL_DELAI_PREPARATION',
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelDeliveryNotificationStr'),
                    'name' => 'TNTOFFICIEL_NOTIFICATION',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionEnabledStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionDisabledStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelViewEDDStr'),
                    'name' => 'TNTOFFICIEL_DATE_PREVISIONNELLE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionEnabledStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionDisabledStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelShowInsuranceStr'),
                    'name' => 'TNTOFFICIEL_INSURANCE',
                    'required' => false,
                    'is_bool' => true,
                    'desc' => TNTOfficiel::getCodeTranslate('descShowInsuranceStr'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionEnabledStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionDisabledStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelPickupTypeStr'),
                    'name' => 'TNTOFFICIEL_TYPE_RAMASSAGE',
                    'required' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'REGULAR',
                                'name' => TNTOfficiel::getCodeTranslate('optionRegularStr'),
                            ),
                            array(
                                'id' => 'OCCASIONAL',
                                'name' => TNTOfficiel::getCodeTranslate('optionOccasionalStr'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelDriverTimeStr'),
                    'class' => 'ramassage col-md-6',
                    'name' => 'TNTOFFICIEL_HEURE_RAMASSAGE_DRIVER',
                    'required' => false,
                    'options' => array(
                        'query' => $arrHeureDriver,
                        'id' => 'idheure',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'class' => 'ramassage col-md-6',
                    'name' => 'TNTOFFICIEL_MINUTE_RAMASSAGE_DRIVER',
                    'required' => false,
                    'options' => array(
                        'query' => $arrMinuteDriver,
                        'id' => 'idminute',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelClosingTimeStr'),
                    'class' => 'ramassage col-md-6',
                    'name' => 'TNTOFFICIEL_HEURE_RAMASSAGE_CLOSING',
                    'required' => false,
                    'options' => array(
                        'query' => $arrHeureClosing,
                        'id' => 'idheure',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'class' => 'ramassage col-md-6',
                    'name' => 'TNTOFFICIEL_MINUTE_RAMASSAGE_CLOSING',
                    'required' => false,
                    'options' => array(
                        'query' => $arrMinuteClosing,
                        'id' => 'idminute',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelShowPickupNumStr'),
                    'name' => 'TNTOFFICIEL_AFFICHAGE_RAMASSAGE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionEnabledStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionDisabledStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => TNTOfficiel::getCodeTranslate('labelAPIKeyGMapStr'),
                    'name' => 'TNTOFFICIEL_GMAP_API_KEY',
                    'size' => 6,
                    'desc' => $strAPIKGMDesc,
                    'required' => false,
                ),
            ),
            'message' => $arrFormMessagesPickup,
        );
    }

    private function getFormAccountZone($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesZone = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array();
        }

        // Input values.
        $arrZone1IDList = Tools::getValue('TNTOFFICIEL_ZONE_1');
        $arrZone2IDList = Tools::getValue('TNTOFFICIEL_ZONE_2');

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_ZONE_1[]'] = $objTNTContextAccountModel->getZone1Departments();
        $arrRefFieldsValue['TNTOFFICIEL_ZONE_2[]'] = $objTNTContextAccountModel->getZone2Departments();
        if (Tools::getIsset($strArgIDForm)) {
            $arrRefFieldsValue['TNTOFFICIEL_ZONE_1[]'] = $arrZone1IDList;
            $arrRefFieldsValue['TNTOFFICIEL_ZONE_2[]'] = $arrZone2IDList;
        }

        /*
         * Validate the Zone and return error messages.
         */

        // If form submitted.
        if (Tools::getIsset($strArgIDForm)) {
            // Check if zones have the same department.
            $boolValid = $objTNTContextAccountModel->setZoneDepartments($arrZone1IDList, $arrZone2IDList);

            // Save and also for each shop in account context.
            $objTNTContextAccountModel->saveContextShop();

            // If no errors.
            if (!$boolValid) {
                $arrFormMessagesZone['error'][] = TNTOfficiel::getCodeTranslate('errorRegionalZoneMutExStr');
            }
        }

        // Department List.
        $arrZoneAllDepartments = $objTNTContextAccountModel->getZoneAllDepartments();
        $objAllDefaultDepartments = array();
        foreach ($arrZoneAllDepartments as $strDepartmentName => $strDepartmentNumber) {
            $objAllDefaultDepartments[] = (object)array(
                'name' => $strDepartmentNumber . ' - ' . $strDepartmentName,
                'id' => $strDepartmentNumber,
            );
        }

        $strZone2Desc = TNTOfficiel::getCodeTranslate('descRegionalZoneStr');

        return array(
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => sprintf(TNTOfficiel::getCodeTranslate('labelRegionalZoneStr'), 1),
                    'name' => 'TNTOFFICIEL_ZONE_1',
                    'size' => 6,
                    'required' => false,
                    'class' => 'chosen col-md-6',
                    'multiple' => true,
                    'options' => array(
                        'query' => $objAllDefaultDepartments,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => sprintf(TNTOfficiel::getCodeTranslate('labelRegionalZoneStr'), 2),
                    'name' => 'TNTOFFICIEL_ZONE_2',
                    'size' => 6,
                    'required' => false,
                    'class' => 'chosen col-md-6',
                    'multiple' => true,
                    'options' => array(
                        'query' => $objAllDefaultDepartments,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $strZone2Desc,
                ),
            ),
            'message' => $arrFormMessagesZone,
        );
    }

    private function getFormAccountOrderState($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesStatus = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array();
        }

        // Defalut values.
        $intOSShipmentSaveID = $objTNTContextAccountModel->os_shipment_save_id;
        $intOSShipmentAfterID = $objTNTContextAccountModel->os_shipment_after_id;
        $intOSParcelTakenInCharge = $objTNTContextAccountModel->os_parcel_takenincharge_id;
        $intOSParcelAllDeliveredID = $objTNTContextAccountModel->os_parcel_alldelivered_id;
        $intOSParcelAllDeliveredToPointID = $objTNTContextAccountModel->os_parcel_alldeliveredtopoint_id;
        $boolOSParcelCheckEnable = $objTNTContextAccountModel->os_parcel_check_enable;
        $intOSParcelCheckRate = $objTNTContextAccountModel->os_parcel_check_rate;

        if (Tools::getIsset($strArgIDForm)) {
            // Input Values.
            $intOSShipmentSaveID = (int)Tools::getValue('TNTOFFICIEL_OS_SHIPMENT_SAVE');
            $intOSShipmentAfterID = (int)Tools::getValue('TNTOFFICIEL_OS_SHIPMENT_AFTER');
            $intOSParcelTakenInCharge = (int)Tools::getValue('TNTOFFICIEL_OS_PARCEL_TAKENINCHARGE');
            $intOSParcelAllDeliveredID = (int)Tools::getValue('TNTOFFICIEL_OS_PARCEL_ALLDELIVERED');
            $intOSParcelAllDeliveredToPointID = (int)Tools::getValue('TNTOFFICIEL_OS_PARCEL_ALLDELIVEREDTOPOINT');
            $boolOSParcelCheckEnable = (bool)Tools::getValue('TNTOFFICIEL_OS_PARCEL_CHECK_ENABLE');
            $intOSParcelCheckRate = (int)Tools::getValue('TNTOFFICIEL_OS_PARCEL_CHECK_RATE');

            if ($intOSShipmentSaveID !== 0
                && $intOSShipmentAfterID === $intOSShipmentSaveID
            ) {
                $arrFormMessagesStatus['error']['TNTOFFICIEL_OS_SHIPMENT_AFTER'] =
                    TNTOfficiel::getCodeTranslate('errorOSShipmentAfterStr');
            }

            /*
             * Save
             */

            // If no errors.
            if (count($arrFormMessagesStatus['error']) === 0) {
                $objTNTContextAccountModel->setOSShipmentSaveID($intOSShipmentSaveID);
                $objTNTContextAccountModel->setOSShipmentAfterID($intOSShipmentAfterID);
                $objTNTContextAccountModel->setOSParcelTakenInChargeID($intOSParcelTakenInCharge);
                $objTNTContextAccountModel->setOSParcelAllDeliveredID($intOSParcelAllDeliveredID);
                $objTNTContextAccountModel->setOSParcelAllDeliveredToPointID($intOSParcelAllDeliveredToPointID);
                $objTNTContextAccountModel->setOSParcelCheckEnable($boolOSParcelCheckEnable);
                if ($boolOSParcelCheckEnable) {
                    $objTNTContextAccountModel->setOSParcelCheckRate($intOSParcelCheckRate);
                } else {
                    // If disabled, select saved, not submitted.
                    $intOSParcelCheckRate = $objTNTContextAccountModel->os_parcel_check_rate;
                }
                // Save and also for each shop in account context.
                $objTNTContextAccountModel->saveContextShop();

                $arrFormMessagesStatus['success'][] = TNTOfficiel::getCodeTranslate('successOrderStateUpdatedStr');
            }
        }

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_OS_SHIPMENT_SAVE'] = $intOSShipmentSaveID;
        $arrRefFieldsValue['TNTOFFICIEL_OS_SHIPMENT_AFTER'] = $intOSShipmentAfterID;
        $arrRefFieldsValue['TNTOFFICIEL_OS_PARCEL_TAKENINCHARGE'] = $intOSParcelTakenInCharge;
        $arrRefFieldsValue['TNTOFFICIEL_OS_PARCEL_ALLDELIVERED'] = $intOSParcelAllDeliveredID;
        $arrRefFieldsValue['TNTOFFICIEL_OS_PARCEL_ALLDELIVEREDTOPOINT'] = $intOSParcelAllDeliveredToPointID;
        $arrRefFieldsValue['TNTOFFICIEL_OS_PARCEL_CHECK_ENABLE'] = $boolOSParcelCheckEnable;
        $arrRefFieldsValue['TNTOFFICIEL_OS_PARCEL_CHECK_RATE'] = $intOSParcelCheckRate;

        // Get all non deleted order state, ordered by name.
        $arrStatusList = OrderState::getOrderStates((int)$this->context->language->id);

        /*
         * Predefined OrderState to include if exist.
         */

        $arrIntOrderStateIDShipmentSave = array(
            (int)Configuration::get('PS_OS_PREPARATION'),
            (int)Configuration::get('PS_OS_SHIPPING'),
        );
        $arrIntOrderStateIDShipmentAfter = array(
            (int)Configuration::get('TNTOFFICIEL_OS_READYFORPICKUP'),
        );
        $arrIntOrderStateIDParcelTakInCharge = array(
            (int)Configuration::get('TNTOFFICIEL_OS_TAKENINCHARGE'),
        );
        $arrIntOrderStateIDParcelAllDelivered = array(
            (int)Configuration::get('PS_OS_DELIVERED'),
        );
        $arrIntOrderStateIDParcelAllDeliveredToPoint = array(
            (int)Configuration::get('TNTOFFICIEL_OS_DELIVEREDTOPOINT'),
        );

        /*
         * OrderState list for select.
         */

        $arrOrderStateOptionsShipmentSave = array(
            0 => array(
                'id' => 0,
                'name' => TNTOfficiel::getCodeTranslate('optionOSNoneStr'),
            ),
        );
        $arrOrderStateOptionsShipmentAfter = array(
            0 => array(
                'id' => 0,
                'name' => TNTOfficiel::getCodeTranslate('optionOSNoneStr'),
            ),
        );
        $arrOrderStateOptionsParcelTakeInCharge = array(
            0 => array(
                'id' => 0,
                'name' => TNTOfficiel::getCodeTranslate('optionOSNoneStr'),
            ),
        );
        $arrOrderStateOptionsParcelAllDelivered = array(
            0 => array(
                'id' => 0,
                'name' => TNTOfficiel::getCodeTranslate('optionOSNoneStr'),
            ),
        );
        $arrOrderStateOptionsParcelAllDeliveredToPoint = array(
            0 => array(
                'id' => 0,
                'name' => TNTOfficiel::getCodeTranslate('optionOSNoneStr'),
            ),
        );

        // For each existing OrderState.
        foreach ($arrStatusList as $arrStatusItem) {
            // If an original or delivery flagged or create by this module or not from
            if ($arrStatusItem['unremovable']
                || $arrStatusItem['delivery']
                || $arrStatusItem['module_name'] === TNTOfficiel::MODULE_NAME
                || $arrStatusItem['module_name'] === ''
            ) {
                $intOSID = (int)$arrStatusItem['id_order_state'];

                // Trigger shipment label creation.
                // If OrderState in preset array.
                // Accept manually created (removable, not module owned) OrderState delivery flagged.
                // (Exclude selected SHIPMENT_AFTER ID).
                if (in_array($intOSID, $arrIntOrderStateIDShipmentSave)
                    || (!$arrStatusItem['unremovable']
                        && $arrStatusItem['delivery']
                        && !$arrStatusItem['module_name']
                    )
                ) {
                    $arrOrderStateOptionsShipmentSave[$intOSID] = array(
                        'id' => $intOSID,
                        'name' => $arrStatusItem['name'],
                    );
                }
                // After shipment label creation.
                // If OrderState in preset array.
                // Accept manually created (removable, not module owned) OrderState delivery flagged.
                // (Exclude selected SHIPMENT_SAVE ID).
                if (in_array($intOSID, $arrIntOrderStateIDShipmentAfter)
                    || (!$arrStatusItem['unremovable']
                        && $arrStatusItem['delivery']
                        && !$arrStatusItem['module_name']
                    )
                ) {
                    $arrOrderStateOptionsShipmentAfter[$intOSID] = array(
                        'id' => $intOSID,
                        'name' => $arrStatusItem['name'],
                    );
                }
                // If OrderState in preset array.
                if (in_array($intOSID, $arrIntOrderStateIDParcelTakInCharge)) {
                    $arrOrderStateOptionsParcelTakeInCharge[$intOSID] = array(
                        'id' => $intOSID,
                        'name' => $arrStatusItem['name'],
                    );
                }
                // If OrderState in preset array.
                if (in_array($intOSID, $arrIntOrderStateIDParcelAllDelivered)) {
                    $arrOrderStateOptionsParcelAllDelivered[$intOSID] = array(
                        'id' => $intOSID,
                        'name' => $arrStatusItem['name'],
                    );
                }
                // If OrderState in preset array.
                if (in_array($intOSID, $arrIntOrderStateIDParcelAllDeliveredToPoint)) {
                    $arrOrderStateOptionsParcelAllDeliveredToPoint[$intOSID] = array(
                        'id' => $intOSID,
                        'name' => $arrStatusItem['name'],
                    );
                }
            }
        }

        ksort($arrOrderStateOptionsShipmentSave);
        ksort($arrOrderStateOptionsShipmentAfter);
        ksort($arrOrderStateOptionsParcelTakeInCharge);
        ksort($arrOrderStateOptionsParcelAllDelivered);
        ksort($arrOrderStateOptionsParcelAllDeliveredToPoint);

        return array(
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSShipmentSaveStr'),
                    'name' => 'TNTOFFICIEL_OS_SHIPMENT_SAVE',
                    'required' => false,
                    'options' => array(
                        'query' => $arrOrderStateOptionsShipmentSave,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSShipmentAfterStr'),
                    'name' => 'TNTOFFICIEL_OS_SHIPMENT_AFTER',
                    'desc' => TNTOfficiel::getCodeTranslate('descOSShipmentAfterStr'),
                    'required' => false,
                    'options' => array(
                        'query' => $arrOrderStateOptionsShipmentAfter,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSParcelTakeStr'),
                    'name' => 'TNTOFFICIEL_OS_PARCEL_TAKENINCHARGE',
                    'desc' => TNTOfficiel::getCodeTranslate('descOSParcelTakeStr'),
                    'required' => false,
                    'options' => array(
                        'query' => $arrOrderStateOptionsParcelTakeInCharge,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<div class="col-md-12"><hr /></div>',
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSParcelCheckStr'),
                    'name' => 'TNTOFFICIEL_OS_PARCEL_CHECK_ENABLE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionEnabledStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionDisabledStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSParcelCheckRateStr'),
                    'name' => 'TNTOFFICIEL_OS_PARCEL_CHECK_RATE',
                    'required' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '10800',
                                'name' => sprintf(
                                    TNTOfficiel::getCodeTranslate('optionOSParcelCheckRateStr'),
                                    (int)(10800 / (60 * 60))
                                ),
                            ),
                            array(
                                'id' => '21600',
                                'name' => sprintf(
                                    TNTOfficiel::getCodeTranslate('optionOSParcelCheckRateStr'),
                                    (int)(21600 / (60 * 60))
                                ),
                            ),
                            array(
                                'id' => '32400',
                                'name' => sprintf(
                                    TNTOfficiel::getCodeTranslate('optionOSParcelCheckRateStr'),
                                    (int)(32400 / (60 * 60))
                                ),
                            ),
                            array(
                                'id' => '43200',
                                'name' => sprintf(
                                    TNTOfficiel::getCodeTranslate('optionOSParcelCheckRateStr'),
                                    (int)(43200 / (60 * 60))
                                ),
                            ),
                            array(
                                'id' => '86400',
                                'name' => sprintf(
                                    TNTOfficiel::getCodeTranslate('optionOSParcelCheckRateStr'),
                                    (int)(86400 / (60 * 60))
                                ),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSParcelAllDeliveredStr')
                        . ' ' . '                                                                ',
                    'name' => 'TNTOFFICIEL_OS_PARCEL_ALLDELIVERED',
                    'required' => false,
                    'options' => array(
                        'query' => $arrOrderStateOptionsParcelAllDelivered,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelOSParcelAllDeliveredPointStr'),
                    'name' => 'TNTOFFICIEL_OS_PARCEL_ALLDELIVEREDTOPOINT',
                    'required' => false,
                    'options' => array(
                        'query' => $arrOrderStateOptionsParcelAllDeliveredToPoint,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
            ),
            'message' => $arrFormMessagesStatus,
        );
    }

    /**
     * Update HRA.
     *
     * @return bool
     */
    public function displayAjaxUpdateHRA()
    {
        TNTOfficiel_Logstack::log();

        $boolSuccess = TNTOfficielCarrier::updateHRAZipCodeList();

        $arrResult = array(
            'result' => $boolSuccess,
        );

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }
}
