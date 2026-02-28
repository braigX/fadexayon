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
 * Class AdminTNTOfficielCarrierController
 */
class AdminTNTOfficielCarrierController extends ModuleAdminController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        TNTOfficiel_Logstack::log();

        // Bootstrap enable.
        $this->bootstrap = true;
        // Apply renderForm method if updatecarrier in URL parameters.
        $this->table = 'carrier';
        $this->className = 'Carrier';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->addRowAction('edit');
        $this->allow_export = false;
        $this->deleted = false;

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => TNTOfficiel::getCodeTranslate('bulkDeleteStr'),
                'confirm' => TNTOfficiel::getCodeTranslate('bulkConfirmDeleteStr'),
                'icon' => 'icon-trash',
            ),
        );

        $this->page_header_toolbar_title = sprintf(
            TNTOfficiel::getCodeTranslate('titleHeaderSetupDeliveryStr'),
            TNTOfficiel::MODULE_TITLE
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();

        $strTablePrefix = _DB_PREFIX_;

        $arrArgIntShopIDList = Shop::getContextListShopID();
        $arrIntShopIDList = array_map('intval', $arrArgIntShopIDList);

        $this->_select = <<<'SQL'
a.id_reference AS `id_reference`,
a.active AS `active`,
a.name AS `name_carrier`,
'0' AS `package_weight`
SQL;

        $this->_join = <<<SQL
LEFT JOIN `${strTablePrefix}tntofficiel_carrier` c ON (c.`id_carrier` = a.`id_carrier`)
JOIN `${strTablePrefix}shop` s ON (s.`id_shop` = c.`id_shop`)
SQL;

        $strIntShopIDList = implode(',', $arrIntShopIDList);
        $this->_where = <<<SQL
AND a.deleted = 0 AND c.id_shop IN (${strIntShopIDList})
SQL;

        // If an account is available for current shop context, and context is Shop (for multistore).
        if ($objTNTContextAccountModel !== null
            && $objTNTContextAccountModel->id_shop > 0
        ) {
            $arrFeasibilityAllCarrierType = $objTNTContextAccountModel->availabilities();
            $arrStrID = array_keys($arrFeasibilityAllCarrierType['arrTNTServiceList']);

            // Filter using carrier that are available for the current account in this shop.
            $strShopIDList = implode(',', $arrIntShopIDList);
            $strCarrierAvailableList = '\'' . implode('\',\'', $arrStrID) . '\'';

            $this->_where = <<<SQL
AND a.deleted = 0 AND c.id_shop IN (${strShopIDList})
-- Filter using available carrier.
AND CONCAT(`account_type`,':',`carrier_type`,':',`carrier_code1`,':',`carrier_code2`) IN (${strCarrierAvailableList})
SQL;
        }

        $this->_orderBy = 'id_reference';
        $this->_orderWay = 'ASC';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_carrier' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldIDStr'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'carrier_label' => array(
                'title' => sprintf(TNTOfficiel::getCodeTranslate('fieldCarrierLabelStr'), TNTOfficiel::MODULE_TITLE),
                // Cell content using callback.
                'callback' => 'getCarrierLabel',
                // Disable table header sort.
                'orderby' => false,
                // Disable table header search.
                'search' => false,
                'filter_key' => 'a!name',
            ),/*
            'image' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldLogoLabelStr'),
                'align' => 'center',
                'image' => 's',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ),*/
            'name_carrier' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldCarrierNameLabelStr'),
                //'orderby' => false,
                'filter_key' => 'a!name',
            ),
            'package_weight' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldParcelLabelStr'),
                // Cell content using callback.
                'callback' => 'printMaxPackageWeight',
                // Disable table header sort.
                'orderby' => false,
                // Disable table header search.
                'search' => false,
                'align' => 'text-right',
                'class' => 'fixed-width-sm',
            ),
            'active' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldStatusStr'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ),
            'is_free' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldFreeLabelStr'),
                'align' => 'center',
                'active' => 'isFree',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ),
            'name_shop_custom' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldShopStr'),
                'filter_key' => 's!name',
                'class' => 'fixed-width-md',
            ),
        );
    }

    /**
     * FIX prefix for filter. Use controller_name property, not classname.
     * Bug appear when using class AdminTNTOfficielCarrierControllerOverride.
     */
    protected function getCookieFilterPrefix()
    {
        $prefix = isset($this->controller_name) ?
            str_replace(array('admin', 'controller'), '', Tools::strtolower($this->controller_name)) : '';

        return $prefix;
    }

    /**
     * FIX prefix for filter. Use controller_name property, not classname.
     * Bug appear when using class AdminTNTOfficielCarrierControllerOverride.
     */
    protected function getCookieOrderByPrefix()
    {
        $prefix = isset($this->controller_name) ?
            str_replace(array('admin', 'controller'), '', Tools::strtolower($this->controller_name)) : '';

        return $prefix;
    }

    /**
     * Add Active/Inactive action for free shipping.
     *
     * @return void
     */
    public function postProcess()
    {
        if (Tools::getIsset('isFree' . $this->table)) {
            $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($this->id_object);
            // If Carrier object is available.
            if ($objPSCarrier !== null) {
                $objPSCarrier->is_free = $objPSCarrier->is_free ? 0 : 1;
                $objPSCarrier->update();
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminTNTOfficielCarrier'));
        } else {
            parent::postProcess();
        }
    }

    public function initPageHeaderToolbar()
    {
        TNTOfficiel_Logstack::log();

        $this->toolbar_title = array($this->breadcrumbs);
        if (is_array($this->breadcrumbs)) {
            $this->toolbar_title = array_unique($this->breadcrumbs);
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }

        $this->toolbar_title = array(
            TNTOfficiel::getCodeTranslate('titleSelectCarrierStr'),
        );

        $this->toolbar_btn = array(/*
            'back' => array(),
            */
        );

        $this->page_header_toolbar_btn = array();

        $this->show_page_header_toolbar = true;

        parent::initPageHeaderToolbar();

        $this->context->smarty->assign(
            array(
                'help_link' => null,
            )
        );
    }

    /**
     * @param $idTntCarrier
     *
     * @return mixed
     */
    public static function getCarrierLabel($echo, $tr)
    {
        TNTOfficiel_Logstack::log();

        // Unused but inherited argument.
        $echo === $echo;

        $intCarrierID = (int)$tr['id_carrier'];

        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        if ($objTNTCarrierModel === null) {
            return null;
        }

        return $objTNTCarrierModel->getCarrierInfos()->label;
    }

    /**
     * @param $pickup_number
     * @param $tr
     *
     * @return string|null
     */
    public function printMaxPackageWeight($echo, $tr)
    {
        TNTOfficiel_Logstack::log();

        // Unused but inherited argument.
        $echo === $echo;

        $intCarrierID = (int)$tr['id_carrier'];

        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        if ($objTNTCarrierModel === null) {
            return null;
        }

        return $objTNTCarrierModel->getMaxPackageWeight() . ' kg';
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

        $this->module->addJS('AdminTNTOfficielCarrier.js');
    }

    public function renderList()
    {
        TNTOfficiel_Logstack::log();

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
        $objHelperForm->base_tpl = 'AdminTNTOfficielCarrier.tpl';

        // Module using this form.
        $objHelperForm->module = $this->module;
        // Controller name.
        $objHelperForm->name_controller = TNTOfficiel::MODULE_NAME;
        // Token.
        $objHelperForm->token = Tools::getAdminTokenLite('AdminTNTOfficielCarrier');
        // Form action attribute.
        $objHelperForm->currentIndex = AdminController::$currentIndex . '&configure=' . TNTOfficiel::MODULE_NAME;

        // Language.
        $objHelperForm->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $objHelperForm->allow_employee_form_lang = (Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            : 0
        );

        // Smarty assign().
        // /modules/<MODULE>/views/templates/admin/_configure/helpers/form/form.tpl
        // extends /<ADMIN>/themes/default/template/helpers/form/form.tpl
        $objHelperForm->tpl_vars['tntofficiel'] = array();

        /*
         * Create Carrier Form
         */

        // Display warning message in the module list for account authentification.
        if ($objTNTContextAccountModel->getAuthValidatedDateTime() === null) {
            $this->warnings[] = TNTOfficiel::getCodeTranslate('warnAuthRequiredStr');
        } else {
            $strIDFormCarrierCreate = 'submit' . TNTOfficiel::MODULE_NAME . 'CarrierCreate';
            $arrFormCarrierCreate = $this->getFormCarrierCreate($strIDFormCarrierCreate, $arrFieldsValue);

            $arrFormStruct[$strIDFormCarrierCreate] = array(
                'form' => array(
                    'legend' => array(
                        'title' => TNTOfficiel::getCodeTranslate('titleFormDeliveryServicesStr'),
                    ),
                    'input' => $arrFormCarrierCreate['input'],
                    'submit' => array(
                        'title' => TNTOfficiel::getCodeTranslate('buttonCreateCarrierStr'),
                        'class' => 'btn btn-default pull-right',
                        'name' => $strIDFormCarrierCreate,
                    ),
                ),
            );
        }

        // Set all form fields values.
        $objHelperForm->fields_value = $arrFieldsValue;

        // Global Submit ID.
        //$objHelperForm->submit_action = 'submit'.TNTOfficiel::MODULE_NAME;
        // Get generated forms.
        $strDisplayForms = $objHelperForm->generateForm($arrFormStruct);

        /*
         * Disabled carrier.
         */

        $arrFormMessagesCarriers = array(
            'info' => array(),
            'warning' => array(),
            'success' => array(),
            'error' => array(),
        );

        /*
        $arrObjTNTCarrierModelList = TNTOfficielCarrier::getContextCarrierModelList(false);

        $arrCarrierDisabled = array();

        // @var \TNTOfficielCarrier $objTNTCarrierModel
        foreach ($arrObjTNTCarrierModelList as $intCarrierID => $objTNTCarrierModel) {
            $objPSCarrier = $objTNTCarrierModel->getPSCarrier();
            // If Carrier object available and not active.
            if ($objPSCarrier !== null && !$objPSCarrier->active) {
                $arrCarrierDisabled[] = $intCarrierID;
            }
        }

        if (count($arrCarrierDisabled) > 0) {
            $arrFormMessagesCarriers['warning'][] = sprintf(
                TNTOfficiel::getCodeTranslate('warnDisabledCarrierStr'),
                'ID '.implode(', ', $arrCarrierDisabled)
            );
        }
        */

        $arrFormCarriersMessageHTML = TNTOfficiel_Tools::getAlertHTML($arrFormMessagesCarriers);
        $strFormCarriersMessageHTML = '<div class="maxwidth-layout">'
            . implode('', $arrFormCarriersMessageHTML)
            . '</div>';

        $strInfoCarrierConfig = '<div class="maxwidth-layout text-right clearfix"><a class="_blank" href='
            . $this->context->link->getAdminLink('AdminCarriers') . '>'
            . TNTOfficiel::getCodeTranslate('linkCarrierOthersModificiationStr')
            . '</a></div>';

        $this->content = $strDisplayForms . parent::renderList() . $strFormCarriersMessageHTML . $strInfoCarrierConfig;

        return '';
    }

    /**
     * Get the Carrier creation form data for Helper.
     *
     * @return array
     */
    private function getFormCarrierCreate($strArgIDForm, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesServiceTnt = array(
            'info' => array(),
            'warning' => array(),
            'success' => array(),
            'error' => array(),
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return array();
        }

        // Input values.
        $strArgServiceTnt = pSQL(Tools::getValue('TNTOFFICIEL_SERVICE_TNT'));

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_SERVICE_TNT'] = null;
        if (Tools::getIsset($strArgIDForm)) {
            $arrRefFieldsValue['TNTOFFICIEL_SERVICE_TNT'] = $strArgServiceTnt;
        }

        $arrFeasibilityAllCarrierType = $objTNTContextAccountModel->availabilities();

        $arrCarrierCode1MapHours = array(
            'N' => '08',
            'A' => '09',
            'T' => '10',
            'M' => '12',
            'J' => '13',
            'P' => '18',
        );

        $arrCarrierTypeMap = array(
            'INDIVIDUAL' => '01',
            'DROPOFFPOINT' => '02',
            'ENTERPRISE' => '03',
            'DEPOT' => '04',
        );

        $objAllCarrierAvailabilities = array();
        foreach ($arrFeasibilityAllCarrierType['arrTNTServiceList'] as $strID => $arrCarrierAvailable) {
            $strCarrierLabel = TNTOfficielCarrier::getCarrierLabel(
                $arrCarrierAvailable['accountType'],
                $arrCarrierAvailable['carrierType'],
                $arrCarrierAvailable['carrierCode1']
            );

            $strSortKeyHour = $arrCarrierAvailable['carrierCode1'];
            if (array_key_exists($arrCarrierAvailable['carrierCode1'], $arrCarrierCode1MapHours)) {
                $strSortKeyHour = $arrCarrierCode1MapHours[$arrCarrierAvailable['carrierCode1']];
            }

            $strSortKeyType = $arrCarrierAvailable['carrierType'];
            if (array_key_exists($arrCarrierAvailable['carrierType'], $arrCarrierTypeMap)) {
                $strSortKeyType = $arrCarrierTypeMap[$arrCarrierAvailable['carrierType']];
            }

            $strSortKey = $strSortKeyType . $arrCarrierAvailable['accountType'] . $strSortKeyHour
                . $strCarrierLabel . $arrCarrierAvailable['carrierCode2'];

            $objAllCarrierAvailabilities[$strSortKey] = (object)array(
                'name' => $strCarrierLabel,
                //'name' => $arrCarrierAvailable['carrierLabel'],
                'id' => $strID,
            );
        }
        // Sort key.
        ksort($objAllCarrierAvailabilities);

        // If form submitted.
        if (Tools::getIsset($strArgIDForm)) {
            $arrCarrierCreate = explode(':', $strArgServiceTnt);
            $boolShopAlreadyAdded = false;
            foreach ($objTNTContextAccountModel->getPSShopList() as $intShopID => $objPSShop) {
                $boolExist = TNTOfficiel::getOption('CARRIER_CREATE_DUPLICATE') !== true
                    && TNTOfficielCarrier::isExist(
                    $intShopID,
                    $arrCarrierCreate[0],
                    $arrCarrierCreate[1],
                    $arrCarrierCreate[2],
                    $arrCarrierCreate[3]
                );
                if ($boolExist) {
                    $boolShopAlreadyAdded = true;
                    break;
                }
            }

            // RG-13
            // Si aucun transporteur n'existe déjà pour ce service de livraison pour les boutiques sélectionnées
            // OU au moins une boutique appartient à la sélection
            if ($boolShopAlreadyAdded === true
                || count($objTNTContextAccountModel->getPSShopList()) === 0
            ) {
                $arrFormMessagesServiceTnt['error']['RG-13'] =
                    TNTOfficiel::getCodeTranslate('errorCreateAlreadyAssociatedStr');
            }

            if (count($arrFormMessagesServiceTnt['error']) === 0) {
                $arrCarrierCreated = $objTNTContextAccountModel->createCarrier(
                    $arrCarrierCreate[0],
                    $arrCarrierCreate[1],
                    $arrCarrierCreate[2],
                    $arrCarrierCreate[3]
                );
                if (!is_array($arrCarrierCreated) || !(count($arrCarrierCreated) > 0)) {
                    $arrFormMessagesServiceTnt['error']['RG-13'] = TNTOfficiel::getCodeTranslate('errorUnknow')
                        . ' ' . TNTOfficiel::getCodeTranslate('errorNoCarrierCreatedStr');
                } else {
                    $strInitCarrierJSON = TNTOfficiel_Tools::encJSON($arrCarrierCreated);
                    $strInitCarrierEncode = TNTOfficiel_Tools::B64URLDeflate($strInitCarrierJSON);

                    Tools::redirectAdmin(
                        $this->context->link->getAdminLink(
                            'AdminTNTOfficielCarrier',
                            true,
                            array(),
                            array(
                                'id_carrier' => $arrCarrierCreated[0],
                                'updatecarrier' => 1,
                                'init_carrier' => urlencode($strInitCarrierEncode),
                            )
                        )
                    );
                }
            }
        }

        $arrFormMessageCarriersConfig['info'][] = TNTOfficiel::getCodeTranslate('infoCreateOneCarrierPerShopStr');

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
                    'name' => implode('', TNTOfficiel_Tools::getAlertHTML($arrFormMessageCarriersConfig)),
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<div class="alert alert-context">'
                        . '<i class="icon-cogs"></i> '
                        . sprintf(
                            TNTOfficiel::getCodeTranslate('alertCreateShopListStr'),
                            TNTOfficiel::MODULE_TITLE,
                            $strHTMLShops
                        ) . '</div>',
                ),
                array(
                    'type' => 'select',
                    'label' => TNTOfficiel::getCodeTranslate('labelSelectCarrierStr'),
                    'name' => 'TNTOFFICIEL_SERVICE_TNT',
                    'class_label' => "col-lg-5",
                    'class_type' => "col-lg-8 col-md-10 col-xs-12",
                    'required' => true,
                    'options' => array(
                        'query' => $objAllCarrierAvailabilities,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => implode('', TNTOfficiel_Tools::getAlertHTML($arrFormMessagesServiceTnt)),
                ),
            ),
        );
    }

    /**
     * Get the Price list form data for Helper.
     *
     * @return array
     */
    private function getFormPriceList($strArgIDFormPriceZone, &$arrRefFieldsValue)
    {
        TNTOfficiel_Logstack::log();

        $arrFormMessagesZones = array(
            'info' => array(),
            'warning' => array(),
            'success' => array(),
            'error' => array(),
        );

        // RG-13
        if (/*!(
            //Aucun transporteur n'existe déjà pour ce service de livraison pour les boutiques sélectionnées
            $shopAdded == true ||
            //Au moins une boutique appartient à la sélection
            count($objTNTContextAccountModel->getPSShopList()) == 0
            )
            // Et en mode création
            &&*/
            Tools::getValue('init_carrier')
        ) { //RG-13
            $arrFormMessagesZones['success']['save'] = TNTOfficiel::getCodeTranslate('successCreateCarrierStr');
        }
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID(Tools::getValue('id_carrier'), false);

        // Input values.
        $strArgSpecificPrice = pSQL(Tools::getValue('TNTOFFICIEL_ZONES_ENABLED'));
        $strArgCloningSpecificPrice = pSQL(Tools::getValue('TNTOFFICIEL_ZONES_CLONING_ENABLED'));
        $arrZonesConfigPost = array_intersect_key(
            (array)Tools::getValue('TNTOFFICIEL_ZONES_CONF'),
            array(array(), array(), array())
        );

        // Displayed values.
        $arrRefFieldsValue['TNTOFFICIEL_ZONES_ENABLED'] = $objTNTCarrierModel->isZonesEnabled();
        $arrRefFieldsValue['TNTOFFICIEL_ZONES_CLONING_ENABLED'] = $objTNTCarrierModel->isZonesCloningEnabled();
        $arrRefFieldsValue['arrZonesConfList'] = $objTNTCarrierModel->getZonesConf();

        if (Tools::getIsset($strArgIDFormPriceZone)) {
            // Display ZonesConf
            if ($strArgSpecificPrice) {
                foreach ($arrZonesConfigPost as $intZoneConfID => &$arrZoneConf) {
                    $arrMessagesZonesKeyLabel = array(
                        0 => TNTOfficiel::getCodeTranslate('tabRateZoneDefault'),
                        1 => sprintf(TNTOfficiel::getCodeTranslate('tabRateZone'), 1),
                        2 => sprintf(TNTOfficiel::getCodeTranslate('tabRateZone'), 2),
                    );
                    $strZoneKeyLabel = $arrMessagesZonesKeyLabel[$intZoneConfID];

                    $strRangeType = pSQL($arrZoneConf['strRangeType']);
                    $fltRangeWeightPricePerKg = pSQL($arrZoneConf['fltRangeWeightPricePerKg']);
                    $fltRangeWeightLimitMax = pSQL($arrZoneConf['fltRangeWeightLimitMax']);
                    $strOutOfRangeBehavior = pSQL($arrZoneConf['strOutOfRangeBehavior']);
                    $fltHRAAdditionalCost = '0';
                    // Optional (unavailable for delivery point).
                    if (array_key_exists('fltHRAAdditionalCost', $arrZoneConf)) {
                        $fltHRAAdditionalCost = pSQL($arrZoneConf['fltHRAAdditionalCost']);
                    }
                    $fltMarginPercent = pSQL($arrZoneConf['fltMarginPercent']);

                    $arrRangeWeightListCol1 = array();
                    if (array_key_exists('arrRangeWeightListCol1', $arrZoneConf)
                        && is_array($arrZoneConf['arrRangeWeightListCol1'])
                    ) {
                        $arrRangeWeightListCol1 = $arrZoneConf['arrRangeWeightListCol1'];
                    }
                    $arrRangeWeightListCol2 = array();
                    if (array_key_exists('arrRangeWeightListCol2', $arrZoneConf)
                        && is_array($arrZoneConf['arrRangeWeightListCol2'])
                    ) {
                        $arrRangeWeightListCol2 = $arrZoneConf['arrRangeWeightListCol2'];
                    }

                    $arrRangePriceListCol1 = array();
                    if (array_key_exists('arrRangePriceListCol1', $arrZoneConf)
                        && is_array($arrZoneConf['arrRangePriceListCol1'])
                    ) {
                        $arrRangePriceListCol1 = $arrZoneConf['arrRangePriceListCol1'];
                    }
                    $arrRangePriceListCol2 = array();
                    if (array_key_exists('arrRangePriceListCol2', $arrZoneConf)
                        && is_array($arrZoneConf['arrRangePriceListCol2'])
                    ) {
                        $arrRangePriceListCol2 = $arrZoneConf['arrRangePriceListCol2'];
                    }

                    $arrZoneConf['arrRangeWeightList'] = array_combine(
                        $arrRangeWeightListCol1,
                        $arrRangeWeightListCol2
                    );
                    $arrZoneConf['arrRangePriceList'] = array_combine(
                        $arrRangePriceListCol1,
                        $arrRangePriceListCol2
                    );

                    $arrRangeWeightList = array();
                    $arrRangePriceList = array();

                    if ($strRangeType === 'weight') {
                        // RG-26 remove lines too much
                        foreach ($arrRangeWeightListCol1 as $key => $weightCol1) {
                            if ($weightCol1 == ''
                                && $arrRangeWeightListCol2[$key] == ''
                            ) {
                                unset($arrRangeWeightListCol1[$key]);
                                unset($arrRangeWeightListCol2[$key]);
                            }
                        }
                        if ($arrRangeWeightListCol1
                            && $arrRangeWeightListCol2
                        ) {
                            $arrRangeWeightList = array_combine($arrRangeWeightListCol1, $arrRangeWeightListCol2);
                        }
                    } else {
                        // RG-26 remove lines too much
                        foreach ($arrRangePriceListCol1 as $key => $weightCol1) {
                            if ($weightCol1 == ''
                                && $arrRangePriceListCol2[$key] == ''
                            ) {
                                unset($arrRangePriceListCol1[$key]);
                                unset($arrRangePriceListCol2[$key]);
                            }
                        }
                        if ($arrRangePriceListCol1
                            && $arrRangePriceListCol2
                        ) {
                            $arrRangePriceList = array_combine($arrRangePriceListCol1, $arrRangePriceListCol2);
                        }
                    }

                    if ($intZoneConfID === 0) {
                        // Error message if specific price is enabled
                        // RG-24
                        $firstCol1 = null;
                        $firstCol2 = null;
                        if ($strRangeType === 'weight') {
                            // Optional.
                            if (array_key_exists(0, $arrRangeWeightListCol1)) {
                                $firstCol1 = $arrRangeWeightListCol1[0];
                            }
                            if (array_key_exists(0, $arrRangeWeightListCol2)) {
                                $firstCol2 = $arrRangeWeightListCol2[0];
                            }
                        } else {
                            // Optional.
                            if (array_key_exists(0, $arrRangePriceListCol1)) {
                                $firstCol1 = $arrRangePriceListCol1[0];
                            }
                            if (array_key_exists(0, $arrRangePriceListCol2)) {
                                $firstCol2 = $arrRangePriceListCol2[0];
                            }
                        }
                        if ((empty($firstCol1) && $firstCol1 != '0')
                            || (empty($firstCol2) && $firstCol2 != '0')
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['list-required'] =
                                TNTOfficiel::getCodeTranslate('errorAtLeastOneRangeForDefaultZoneStr');
                        }
                        //------RG-24
                    }
                    if ($strRangeType === 'weight') {
                        $cols1 = $arrRangeWeightListCol1;
                        $cols2 = $arrRangeWeightListCol2;
                        // RG-19
                        if (!empty($fltRangeWeightPricePerKg)
                            && !(is_numeric($fltRangeWeightPricePerKg)
                                && (Tools::strlen(Tools::substr(strrchr($fltRangeWeightPricePerKg, "."), 7)) == 0)
                            )
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-19-priceSupp'] =
                                TNTOfficiel::getCodeTranslate('errorExtraKgPriceFormatStr');
                        }
                        // RG-18
                        if (!empty($fltRangeWeightLimitMax)
                            && !(is_numeric($fltRangeWeightLimitMax)
                                && (Tools::strlen(Tools::substr(strrchr($fltRangeWeightLimitMax, "."), 2)) == 0)
                            )
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-18-limite'] =
                                TNTOfficiel::getCodeTranslate('errorLimitFormatStr');
                        }
                    } else {
                        $cols1 = $arrRangePriceListCol1;
                        $cols2 = $arrRangePriceListCol2;
                    }

                    // RG-19
                    if (!empty($fltHRAAdditionalCost)
                        && !(is_numeric($fltHRAAdditionalCost)
                            && (Tools::strlen(Tools::substr(strrchr($fltHRAAdditionalCost, "."), 7)) == 0)
                        )
                    ) {
                        $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-19-difficultArea'] =
                            TNTOfficiel::getCodeTranslate('errorHRAFormatStr');
                    }
                    // RG-20
                    if (!empty($fltMarginPercent)
                        && !(is_numeric($fltMarginPercent)
                            && (Tools::strlen(Tools::substr(strrchr($fltMarginPercent, "."), 3)) == 0)
                            && $fltMarginPercent >= 0
                            && $fltMarginPercent <= 100
                        )
                    ) {
                        $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-20-marge'] =
                            TNTOfficiel::getCodeTranslate('errorMarginFormatStr');
                    }
                    foreach ($cols1 as $key => $col1) {
                        // RG-18
                        if ($strRangeType === 'weight'
                            && !empty($col1)
                            && !(is_numeric($col1)
                                && (Tools::strlen(Tools::substr(strrchr($col1, '.'), 2)) == 0)
                            )
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-18-borne-superieure'] =
                                TNTOfficiel::getCodeTranslate('errorWeightUpperBoundStr');
                        }
                        // RG-19
                        if ($strRangeType === 'price'
                            && !empty($col1)
                            && !(is_numeric($col1)
                                && (Tools::strlen(Tools::substr(strrchr($col1, "."), 7)) == 0)
                            )
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-19-borne-superieure'] =
                                TNTOfficiel::getCodeTranslate('errorPriceUpperBoundStr');
                        }
                        // RG-25
                        if (empty($col1)
                            && $col1 != '0'
                            && !empty($cols2[$key])
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['list-required'] =
                                TNTOfficiel::getCodeTranslate('errorRangeIncompleteStr');
                        }
                    }
                    foreach ($cols2 as $key => $col2) {
                        // RG-19
                        if (!empty($col2)
                            && !(is_numeric($col2)
                                && (Tools::strlen(Tools::substr(strrchr($col2, "."), 7)) == 0)
                            )
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-19-prix'] =
                                TNTOfficiel::getCodeTranslate('errorPriceFormatStr');
                        }
                        // RG-25
                        if (empty($col2)
                            && $col2 != '0'
                            && !empty($cols1[$key])
                        ) {
                            $arrFormMessagesZones['error'][$strZoneKeyLabel]['list-required'] =
                                TNTOfficiel::getCodeTranslate('errorRangeIncompleteStr');
                            //break;
                        }
                    }

                    // RG-29
                    //to delete empty lines
                    $cols1Values = array_values(array_filter($cols1));
                    $sortedCols1 = $cols1Values;
                    sort($sortedCols1);
                    // if list cols1 not null and not sorted or have same value
                    if (($cols1 && ($cols1Values !== $sortedCols1))
                        || !(array_unique($cols1) == $cols1)
                    ) {
                        $arrFormMessagesZones['error'][$strZoneKeyLabel]['RG-29'] =
                            TNTOfficiel::getCodeTranslate('errorRangeMustAscOrderedStr');
                    }
                    //-----RG-29

                    // No error, then save zone config.
                    if (!array_key_exists($strZoneKeyLabel, $arrFormMessagesZones['error'])
                        || count($arrFormMessagesZones['error'][$strZoneKeyLabel]) === 0
                    ) {
                        // foreach multi shop save all zones (3zones)
                        $strInitCarrierEncode = (string)Tools::getValue('init_carrier');
                        $strInitCarrierJSON = TNTOfficiel_Tools::B64URLInflate($strInitCarrierEncode);
                        $arrInitCarrier = TNTOfficiel_Tools::decJSON($strInitCarrierJSON);
                        // Add
                        if ($arrInitCarrier) {
                            foreach ($arrInitCarrier as $intCarrierID) {
                                $objTNTCarrierInitModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                                $objTNTCarrierInitModel->setZoneRangeType($intZoneConfID, $strRangeType);
                                $objTNTCarrierInitModel->setZoneRangeWeightList(
                                    $intZoneConfID,
                                    $arrRangeWeightList,
                                    $fltRangeWeightPricePerKg,
                                    $fltRangeWeightLimitMax
                                );
                                $objTNTCarrierInitModel->setZoneRangePriceList($intZoneConfID, $arrRangePriceList);
                                $objTNTCarrierInitModel->setZoneOutOfRangeBehavior(
                                    $intZoneConfID,
                                    $strOutOfRangeBehavior
                                );
                                $objTNTCarrierInitModel->setZoneHRAAdditionalCost(
                                    $intZoneConfID,
                                    $fltHRAAdditionalCost
                                );
                                $objTNTCarrierInitModel->setZoneMarginPercent($intZoneConfID, $fltMarginPercent);
                            }
                        } else {
                            // Add for a single shop or edit without cloning
                            $objTNTCarrierModel->setZoneRangeType($intZoneConfID, $strRangeType);
                            if ($strRangeType === 'weight') {
                                $objTNTCarrierModel->setZoneRangeWeightList(
                                    $intZoneConfID,
                                    $arrRangeWeightList,
                                    $fltRangeWeightPricePerKg,
                                    $fltRangeWeightLimitMax
                                );
                            } else {
                                $objTNTCarrierModel->setZoneRangePriceList($intZoneConfID, $arrRangePriceList);
                            }
                            $objTNTCarrierModel->setZoneOutOfRangeBehavior($intZoneConfID, $strOutOfRangeBehavior);
                            $objTNTCarrierModel->setZoneHRAAdditionalCost($intZoneConfID, $fltHRAAdditionalCost);
                            $objTNTCarrierModel->setZoneMarginPercent($intZoneConfID, $fltMarginPercent);
                            // Cloning
                            $arrCarriersSelected = Tools::getValue('carriersSelected');
                            if (is_array($arrCarriersSelected)) {
                                foreach ($arrCarriersSelected as $intCarrierID) {
                                    $objTNTCarrierSelectedModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                                    $objTNTCarrierSelectedModel->setZoneRangeType($intZoneConfID, $strRangeType);
                                    if ($strRangeType === 'weight') {
                                        $objTNTCarrierSelectedModel->setZoneRangeWeightList(
                                            $intZoneConfID,
                                            $arrRangeWeightList,
                                            $fltRangeWeightPricePerKg,
                                            $fltRangeWeightLimitMax
                                        );
                                    } else {
                                        $objTNTCarrierSelectedModel->setZoneRangePriceList(
                                            $intZoneConfID,
                                            $arrRangePriceList
                                        );
                                    }
                                    $objTNTCarrierSelectedModel->setZoneOutOfRangeBehavior(
                                        $intZoneConfID,
                                        $strOutOfRangeBehavior
                                    );
                                    $objTNTCarrierSelectedModel->setZoneHRAAdditionalCost(
                                        $intZoneConfID,
                                        $fltHRAAdditionalCost
                                    );
                                    $objTNTCarrierSelectedModel->setZoneMarginPercent(
                                        $intZoneConfID,
                                        $fltMarginPercent
                                    );
                                }
                            }
                        }
                    }
                }
            }
            $arrRefFieldsValue['TNTOFFICIEL_ZONES_ENABLED'] = $strArgSpecificPrice;
            $arrRefFieldsValue['TNTOFFICIEL_ZONES_CLONING_ENABLED'] = $strArgCloningSpecificPrice;
            $arrRefFieldsValue['arrZonesConfList'] = $arrZonesConfigPost;

            if (count($arrFormMessagesZones['error']) === 0) {
                // foreach multi shop save zonesEnabled
                $strInitCarrierEncode = (string)Tools::getValue('init_carrier');
                $strInitCarrierJSON = TNTOfficiel_Tools::B64URLInflate($strInitCarrierEncode);
                $arrInitCarrier = TNTOfficiel_Tools::decJSON($strInitCarrierJSON);
                if ($arrInitCarrier) {
                    foreach ($arrInitCarrier as $intCarrierID) {
                        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                        $objTNTCarrierModel->setZonesEnabled($strArgSpecificPrice);
                        $objTNTCarrierModel->setZonesCloningEnabled($strArgCloningSpecificPrice);
                    }
                } else {
                    $objTNTCarrierModel->setZonesEnabled($strArgSpecificPrice);
                    $objTNTCarrierModel->setZonesCloningEnabled($strArgCloningSpecificPrice);
                }

                $arrFormMessagesZones['success']['save'] = TNTOfficiel::getCodeTranslate('successSaveStr');
            }
        }
        // 6.3.3.1
        $arrFormMessagesZones['info'][] = sprintf(
            TNTOfficiel::getCodeTranslate('infoItemWeightStr'),
            $objTNTCarrierModel->getMaxPackageWeight()
        );

        if (count($arrFormMessagesZones['error']) > 0) {
            unset($arrFormMessagesZones['success']);
        }

        // foreach multi shop get Carrier model name
        $strInitCarrierEncode = (string)Tools::getValue('init_carrier');
        $strInitCarrierJSON = TNTOfficiel_Tools::B64URLInflate($strInitCarrierEncode);
        $arrInitCarrier = TNTOfficiel_Tools::decJSON($strInitCarrierJSON);
        if (!$arrInitCarrier) {
            $arrInitCarrier = (array)Tools::getValue('id_carrier');
        }

        $arrCarrierInfoList = array();
        $arrZonesInfoList = array(
            'showTab' => array(
                '0' => false,
                '1' => false,
                '2' => false,
            ),
            'html' => array(
                '0' => '',
                '1' => '',
                '2' => '',
            ),
            'shop' => array(),
        );

        if ($arrInitCarrier) {
            foreach ($arrInitCarrier as $intCarrierID) {
                $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                $strShopName = $objTNTCarrierModel->getPSShop()->name;
                $arrCarrierInfoList[$intCarrierID] = array(
                    'shop' => $strShopName,
                    'carrier' => $objTNTCarrierModel->getCarrierInfos()->label,
                    'tax' => $objTNTCarrierModel->getTaxInfos('FR'),
                );

                $objTNTAccountModel = $objTNTCarrierModel->getTNTAccountModel();
                if (!array_key_exists($strShopName, $arrZonesInfoList['shop'])) {
                    $arrZonesInfoList['shop'][$strShopName] = array(
                        '0' => $objTNTAccountModel->getZoneDefaultDepartments(),
                        '1' => $objTNTAccountModel->getZone1Departments(),
                        '2' => $objTNTAccountModel->getZone2Departments(),
                    );
                    foreach ($arrZonesInfoList['shop'][$strShopName] as $z => $av) {
                        foreach ($av as $dn => $di) {
                            $arrZonesInfoList['shop'][$strShopName][$z][$dn] = sprintf('%s (%s)', $dn, $di);
                        }
                        $arrZonesInfoList['shop'][$strShopName][$z] = implode(
                            ', ',
                            $arrZonesInfoList['shop'][$strShopName][$z]
                        );
                    }
                }
            }
            foreach ($arrZonesInfoList['shop'] as $strShopName => $arrZoneInfoShop) {
                foreach ($arrZoneInfoShop as $z => $sv) {
                    $arrZonesInfoList['showTab'][$z] = $arrZonesInfoList['showTab'][$z] || ($sv !== '');
                }
            }

            foreach ($arrZonesInfoList['shop'] as $strShopName => $arrZoneInfoShop) {
                foreach ($arrZoneInfoShop as $z => $sv) {
                    $arrZonesInfoList['html'][$z] .= '<li>' . sprintf(
                            '%s: %s',
                            '<b>' . $strShopName . '</b>',
                            (($sv !== '') ? $sv . '.' : '-')
                        ) . '</li>';
                }
            }
        }

        // Pass trough fields values
        $arrRefFieldsValue['arrZonesInfoList'] = $arrZonesInfoList;

        $strHTMLServices = '';
        foreach ($arrCarrierInfoList as $arrCarrierInfoItem) {
            $strHTMLTax = '<li>' . TNTOfficiel::getCodeTranslate('infoNoTaxStr') . '</li>';

            if (is_string($arrCarrierInfoItem['tax']['group'])) {
                $strHTMLTax = '<li>' . sprintf(
                        TNTOfficiel::getCodeTranslate('infoTaxStr'),
                        '<b>' . $arrCarrierInfoItem['tax']['group'] . '</b>',
                        '<b>' . $arrCarrierInfoItem['tax']['country'] . '</b>',
                        '<b>' . number_format($arrCarrierInfoItem['tax']['rate'], 2, ',', ' ') . '%' . '</b>',
                        $arrCarrierInfoItem['tax']['name']
                    ) . '</li>';
            }

            $strHTMLServices .= '<li>' . sprintf(
                    TNTOfficiel::getCodeTranslate('infoOnStr'),
                    '<b>' . $arrCarrierInfoItem['carrier'] . '</b>',
                    '<b>' . $arrCarrierInfoItem['shop'] . '</b>'
                ) . '<ul>' . $strHTMLTax . '</ul>' . '</li>';
        }
        $strHTMLServices = '<ul>' . $strHTMLServices . '</ul>';

        $arrFormMessagesInfo = array('info' => $arrFormMessagesZones['info']);
        unset($arrFormMessagesZones['info']);

        return array(
            'input' => array(
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<div class="alert alert-context">'
                        . sprintf(TNTOfficiel::getCodeTranslate('alertModifiedCarrierListStr'), $strHTMLServices)
                        . '</div>',
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => implode('', TNTOfficiel_Tools::getAlertHTML($arrFormMessagesZones)),
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => implode('', TNTOfficiel_Tools::getAlertHTML($arrFormMessagesInfo)),
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelTNTRateStr'),
                    'name' => 'TNTOFFICIEL_ZONES_ENABLED',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionYesStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionNoStr'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => TNTOfficiel::getCodeTranslate('labelTNTRateCloneStr'),
                    'name' => 'TNTOFFICIEL_ZONES_CLONING_ENABLED',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => TNTOfficiel::getCodeTranslate('optionYesStr'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => TNTOfficiel::getCodeTranslate('optionNoStr'),
                        ),
                    ),
                ),
            ),
            'message' => $arrFormMessagesZones,
        );
    }

    /**
     * @param $productA
     * @param $productB
     *
     * @return int
     */
    public static function cmp($productA, $productB)
    {
        TNTOfficiel_Logstack::log();

        return strcmp($productA["shop"], $productB["shop"]);
    }

    /**
     * @param $productA
     * @param $productB
     *
     * @return int
     */
    public static function cmpService($productA, $productB)
    {
        TNTOfficiel_Logstack::log();

        return strcmp($productA["service_label"], $productB["service_label"]);
    }

    public function renderForm()
    {
        TNTOfficiel_Logstack::log();

        $strArgCarrierID = (string)Tools::getValue('id_carrier');

        $strInitCarrierEncode = (string)Tools::getValue('init_carrier');
        $strInitCarrierJSON = TNTOfficiel_Tools::B64URLInflate($strInitCarrierEncode);
        $arrInitCarrier = TNTOfficiel_Tools::decJSON($strInitCarrierJSON);
        if (!$arrInitCarrier) {
            $arrInitCarrier = (array)Tools::getValue('id_carrier');
        }

        $arrArgCarriersSelected = Tools::getValue('carriersSelected');
        if (!is_array($arrArgCarriersSelected)) {
            $arrArgCarriersSelected = array();
        }

        $objHelperForm = new HelperForm();
        $arrFieldsValue = array();

        /*
         * Create Price Zone Form
         */

        // Form Structure used as parameter for Helper 'generateForm' method.
        $arrFormStruct = array();

        $strIDFormPriceZone = 'submit' . TNTOfficiel::MODULE_NAME . 'CarrierCreate';
        $arrFormPriceList = $this->getFormPriceList($strIDFormPriceZone, $arrFieldsValue);
        $arrFormInputZoneOnglet = $arrFormPriceList['input'];

        //$objHelperForm->base_folder = 'helpers/form/';
        $objHelperForm->base_tpl = 'AdminPriceSetting.tpl';

        // Module using this form.
        $objHelperForm->module = $this->module;
        // Controller name.
        $objHelperForm->name_controller = TNTOfficiel::MODULE_NAME;
        // Token.
        $objHelperForm->token = Tools::getAdminTokenLite('AdminTNTOfficielCarrier');
        // Form action attribute.
        // Input values.
        $objHelperForm->currentIndex = AdminController::$currentIndex
            . '&id_carrier=' . $strArgCarrierID
            . '&updatecarrier'
            . '&init_carrier=' . $strInitCarrierEncode;

        // Language.
        $objHelperForm->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $objHelperForm->allow_employee_form_lang = (Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') :
            0
        );

        $arrCarrierCloningList = array();
        // In edit mode return list carrier except the current carrier (edited carrier)
        if ($strInitCarrierEncode === '') {
            $arrCarrierCloningList = TNTOfficielCarrier::getContextCarrierModelList(false);
            unset($arrCarrierCloningList[$strArgCarrierID]);
        }

        $arrCarrierCloningTableList = array();
        /** @var \TNTOfficielCarrier $objTNTCarrierModel */
        foreach ($arrCarrierCloningList as $intCarrierID => $objTNTCarrierModel) {
            $objPSShop = $objTNTCarrierModel->getPSShop();
            $arrCarrierCloningTableList[] = array(
                'checkedValue' => in_array($intCarrierID, $arrArgCarriersSelected),
                'carrier_id' => $intCarrierID,
                'service_label' => $objTNTCarrierModel->getCarrierInfos()->label,
                'carrier_name' => $objTNTCarrierModel->getName(),
                'shop' => $objPSShop->name,
            );
        }

        // CC triée sur la boutique puis sur le service TNT.
        usort($arrCarrierCloningTableList, array(__CLASS__, 'cmpService'));
        usort($arrCarrierCloningTableList, array(__CLASS__, 'cmp'));

        $arrFormStruct[$strIDFormPriceZone] = array(
            'form' => array(
                'legend' => array(
                    'title' => TNTOfficiel::getCodeTranslate('titleFormRateSettingStr'),
                ),
                'input' => $arrFormInputZoneOnglet,
                'buttons' => array(
                    'back' => array(
                        'title' => TNTOfficiel::getCodeTranslate('buttonBackToListStr'),
                        'href' => $this->context->link->getAdminLink('AdminTNTOfficielCarrier'),
                        'icon' => 'process-icon-back',
                    ),
                ),
                'submit' => array(
                    'title' => TNTOfficiel::getCodeTranslate('buttonSaveStr'),
                    'class' => 'btn btn-default pull-right',
                    'name' => $strIDFormPriceZone,
                ),
            ),
        );

        $boolIsHRAApplicable = false;
        foreach ($arrInitCarrier as $intCarrierID) {
            $intCarrierID = (int)$intCarrierID;
            $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
            if ($objTNTCarrierModel !== null) {
                $boolIsHRAApplicable = $boolIsHRAApplicable || $objTNTCarrierModel->isHRAApplicable();
            }
        }

        // /modules/<MODULE>/views/templates/admin/_configure/helpers/form/form.tpl
        // extends /<ADMIN>/themes/default/template/helpers/form/form.tpl
        $objHelperForm->tpl_vars['tntofficiel'] = array(
            'isHRAApplicable' => $boolIsHRAApplicable,
            'arrZonesConfList' => $arrFieldsValue['arrZonesConfList'],
            'arrZonesInfoList' => $arrFieldsValue['arrZonesInfoList'],
            'arrCarrierCloningTableList' => $arrCarrierCloningTableList,
        );

        // Set all form fields values.
        $objHelperForm->fields_value = $arrFieldsValue;

        // Global Submit ID.
        //$objHelperForm->submit_action = 'submit'.TNTOfficiel::MODULE_NAME;
        // Get generated forms.
        $strZoneForms = $objHelperForm->generateForm($arrFormStruct);

        /*
         * Disabled carrier.
         */

        /*
        $arrFormMessagesCarriers = array(
            'info' => array(),
            'warning' => array(),
            'success' => array(),
            'error' => array(),
        );

        $arrCarrierDisabled = array();

        foreach ($arrInitCarrier as $strArgCarrierID) {
            $intArgCarrierID = (int)$strArgCarrierID;
            $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intArgCarrierID, false);
            if($objTNTCarrierModel !== null) {
                $objPSCarrier = $objTNTCarrierModel->getPSCarrier();
                // If Carrier object available and not active.
                if ($objPSCarrier !== null && !$objPSCarrier->active) {
                    $arrCarrierDisabled[] = $intCarrierID;
                }
            }

            if (count($arrCarrierDisabled) > 0) {
                $arrFormMessagesCarriers['warning'][] = sprintf(
                    TNTOfficiel::getCodeTranslate('warnDisabledCarrierStr'),
                    'ID '.implode(', ', $arrCarrierDisabled)
                );
            }
        }*/

        return $strZoneForms . parent::renderForm();
    }
}
