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
 * Class AdminTNTOfficielStatusController
 */
class AdminTNTOfficielStatusController extends ModuleAdminController
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

        //$this->module->addJS('AdminTNTOfficielStatus.js');
    }

    /**
     * Display page.
     */
    public function renderList()
    {
        TNTOfficiel_Logstack::log();

        parent::renderList();

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
        //$objHelperForm->base_tpl = 'form.tpl';

        // Module using this form.
        $objHelperForm->module = $this->module;
        // Controller name.
        $objHelperForm->name_controller = TNTOfficiel::MODULE_NAME;
        // Token.
        $objHelperForm->token = Tools::getAdminTokenLite('AdminTNTOfficielStatus');
        // Form action attribute.
        $objHelperForm->currentIndex = AdminController::$currentIndex . '&configure=' . TNTOfficiel::MODULE_NAME;

        // Language.
        $objHelperForm->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $objHelperForm->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        /*
         * Configuration Form
         */

        $arrFormInputStatus = array();
        $arrAllMessageList = array();

        $strIDFormStatus = 'submit' . TNTOfficiel::MODULE_NAME . 'Status';

        // Form sender.
        $arrFormSender = $this->getFormAccountSender($strIDFormStatus, $arrFieldsValue);

        $arrFormInputStatus = array_merge(
            $arrFormInputStatus,
            $arrFormSender['input']
        );

        $arrAllMessageList[] = $arrFormSender['message'];

        $arrFormRequiredStatus = array();
        $arrFormRequiredStatusLabel = array();
        if (Tools::getIsset($strIDFormStatus)) {
            foreach ($arrFormInputStatus as $arrField) {
                if (array_key_exists('required', $arrField) && $arrField['required'] === true) {
                    if (array_key_exists($arrField['name'], $arrFieldsValue)
                        && $arrFieldsValue[$arrField['name']] === ''
                    ) {
                        // Red highlight if error on field.
                        $arrFormRequiredStatus[] = $arrField['name'];
                        // List required fields names for error message.
                        $arrFormRequiredStatusLabel[] = $arrField['label'];
                    }
                }
            }

            foreach ($arrAllMessageList as $arrErrorCopy) {
                foreach ($arrErrorCopy['error'] as $mxdFieldName => $strErrorMsg) {
                    if (is_string($mxdFieldName)) {
                        // Red highlight if error on field.
                        $arrFormRequiredStatus[] = $mxdFieldName;
                    }
                }
            }
        }

        /*
         * Merge messages.
         */

        $arrFormMessageStatus = array(
            'error' => array(),
            'warning' => array(),
            'success' => array(),
        );

        if (Tools::getIsset($strIDFormStatus)) {
            foreach ($arrFormMessageStatus as $strType => $arrValue) {
                foreach ($arrAllMessageList as $arrAlertCopy) {
                    if (array_key_exists($strType, $arrAlertCopy)) {
                        foreach ($arrAlertCopy[$strType] as $mxdFieldName => $strValueCopy) {
                            if (is_string($mxdFieldName)) {
                                $arrFormMessageStatus[$strType][$mxdFieldName] = $strValueCopy;
                            } else {
                                $arrFormMessageStatus[$strType][] = $strValueCopy;
                            }
                        }
                    }
                }
            }

            // Add required message.
            if (count($arrFormRequiredStatusLabel) === 1) {
                $arrFormMessageStatus['error'][$arrFormRequiredStatus[0]] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorFieldMandatoryPleaseCheckStr'),
                    implode(', ', $arrFormRequiredStatusLabel)
                );
            } elseif (count($arrFormRequiredStatusLabel) > 1) {
                $arrFormMessageStatus['error'][] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorFieldsMandatoryPleaseCheckStr'),
                    implode(', ', $arrFormRequiredStatusLabel)
                );
            }
        }

        if (count($arrFormMessageStatus['error']) > 0) {
            // If error, do not display success.
            unset($arrFormMessageStatus['success']);
        } elseif (count($arrFormMessageStatus['success']) > 0) {
            // Use only one common success message.
            $arrFormMessageStatus['success'] = array(
                TNTOfficiel::getCodeTranslate('successSettingsUpdatedStr'),
            );
        }

        // HTML Formatting.
        $arrFormStatusMessageInput = array();
        $arrFormStatusMessageHTML = TNTOfficiel_Tools::getAlertHTML($arrFormMessageStatus);
        if (count($arrFormStatusMessageHTML) > 0) {
            $arrFormStatusMessageInput = array(
                array(
                    'type' => 'html',
                    'name' => implode('', $arrFormStatusMessageHTML),
                ),
            );
        }

        // Add form.
        $arrFormStruct[$strIDFormStatus] = array(
            'form' => array(
                'legend' => array(
                    'title' => TNTOfficiel::getCodeTranslate('State'),
                ),
                'input' => array_merge($arrFormStatusMessageInput, $arrFormInputStatus),
                'submit' => array(
                    'title' => TNTOfficiel::getCodeTranslate('buttonValidate'),
                    'class' => 'btn btn-default pull-right',
                    'name' => $strIDFormStatus,
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
         * (Footer)
         */

        return '';
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
        //$objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        $strConfig =
            '<h4>Prestashop</h4>'
            . '<pre class="alert" style="white-space: pre-wrap;">'
            . htmlentities(TNTOfficiel_Tools::encJSON(TNTOfficiel_Tools::getPSConfig()))
            . '</pre>'
            . '<h4>PHP</h4>'
            . '<pre class="alert" style="white-space: pre-wrap;">'
            . htmlentities(TNTOfficiel_Tools::encJSON(TNTOfficiel_Tools::getPHPConfig()))
            . '</pre>';

        $strModule =
            '<h4>TNT</h4>'
            . '<pre class="alert" style="white-space: pre-wrap;">'
            . htmlentities(TNTOfficiel_Tools::encJSON(TNTOfficiel_Tools::getLocationInfo()))
            . '</pre>'
            . '<h4>Module</h4>'
            . '<pre class="alert" style="white-space: pre-wrap;">'
            . htmlentities(TNTOfficiel_Tools::encJSON(TNTOfficiel_Tools::getModuleState(TNTOfficiel::MODULE_NAME)))
            . '</pre>';

        /*
         * Validate and return error messages.
         */

        $strState = '';

        // Defalut values.
        $intURLMyIP = 1;

        // If store request (else simple check).
        if (Tools::getIsset($strArgIDForm)) {
            // Input values.
            // ObjectModel self escape data with formatValue(). Do not double escape using pSQL.
            $intURLMyIP = trim((string)Tools::getValue('TNTOFFICIEL_URLMYIP'));
        }

        $arrMyURLOptions = array(
            1 => 'http://whatismyip.akamai.com/',
            2 => 'http://checkip.amazonaws.com/',
            3 => 'https://ip4.seeip.org/', // 'https://ip6.seeip.org/',
            4 => 'https://api.ipify.org/', // 'https://api64.ipify.org/',
            //5 => 'https://api.my-ip.io/ip',
            //6 => 'http://ifconfig.co/ip',
            //7 => 'http://ifconfig.me/ip',
            //8 => 'http://myexternalip.com/raw',
        );

        if (array_key_exists($intURLMyIP, $arrMyURLOptions)) {
            $strURLMyIP = $arrMyURLOptions[$intURLMyIP];
        }

        if (!Validate::isUrl($strURLMyIP)) {
            //parse_url($strURLMyIP, PHP_URL_SCHEME);
            //parse_url($strURLMyIP, PHP_URL_HOST);
            //parse_url($strURLMyIP, PHP_URL_PATH);

            $strURLMyIP = null;
            $arrFormMessagesSender['error']['TNTOFFICIEL_URLMYIP'] =
                TNTOfficiel::getCodeTranslate('errorInvalidEmailStr');
        }

        /*
         * Save
         */

        // If no errors.
        if (count($arrFormMessagesSender['error']) === 0) {
            $arrFormMessagesSender['success'][] = TNTOfficiel::getCodeTranslate('successUpdateSuccessful');

            $arrState = TNTOfficiel_Tools::getHTTPRequestState(
                TNTOfficiel_SoapClient::URL_WSDL,
                $strURLMyIP
            );
            $strState = '<h4>' . TNTOfficiel_SoapClient::URL_WSDL . '</h4>' .
                '<pre class="alert" style="white-space: pre-wrap;">'
                . htmlentities(TNTOfficiel_Tools::encJSON($arrState))
                . '</pre>';
        }

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_URLMYIP'] = $intURLMyIP;

        $arrMyURLOptions = array(
            array(
                'id' => 1,
                'name' => 'whatismyip.akamai.com',
            ),
            array(
                'id' => 2,
                'name' => 'checkip.amazonaws.com',
            ),
            array(
                'id' => 3,
                'name' => 'ip4.seeip.org',
            ),
            array(
                'id' => 4,
                'name' => 'api.ipify.org',
            ),
        );

        return array(
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('URL'),
                    'name' => 'TNTOFFICIEL_URLMYIP',
                    'required' => false,
                    'options' => array(
                        'query' => $arrMyURLOptions,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => $strState,
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => $strModule,
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => $strConfig,
                ),
            ),
            'message' => $arrFormMessagesSender,
        );
    }
}
