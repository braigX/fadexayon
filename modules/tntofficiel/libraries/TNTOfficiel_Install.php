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
 * Class TNTOfficiel_Install
 * Used in upgrade, do not rename or remove.
 */
class TNTOfficiel_Install
{
    /** @var array */
    public static $arrHookList = array(
        // Header
        'displayBackOfficeHeader',
        'actionAdminControllerSetMedia',
        'displayHeader',

        // Front-Office display carrier.
        'displayBeforeCarrier',
        'displayAfterCarrier',
        'displayCarrierExtraContent',
        // PS 1.7.1+.
        'actionValidateStepComplete',
        // Front-Office order detail.
        'displayOrderDetail',

        // Order created.
        'actionValidateOrder',

        // Order status before changed.
        'actionOrderStatusUpdate',
        // Order status after changed.
        'actionOrderStatusPostUpdate',
        // Order status added.
        'actionOrderHistoryAddAfter',

        // Back-Office order detail.
        'displayAdminOrder',
        // PS 1.7.7+.
        'ActionGetAdminOrderButtons',
        // PS 1.7.7+.
        'displayAdminOrderSide',
        // Carrier updated.
        'actionCarrierUpdate',

        // Add variables for email.
        'actionGetExtraMailTemplateVars',

        // action<ClassName><Action>Before
        'actionAdminCartsControllerBefore',

        //'actionAdminMetaControllerUpdate_optionsBefore',
        //'actionCarrierProcess',
        //'actionOrderDetail',
        //'actionValidateCustomerAddressForm',
        //'validateCustomerFormFields',

        //'actionAdminCarriersControllerDeleteAfter',
        //'actionFrontControllerSetMedia',
        //'displayCarrierList',
        //'actionBeforeAjaxDieOrderOpcControllerinit',
        //'actionObjectAddressUpdateBefore',
        //'actionObjectAddressDeleteBefore',
    );

    /** @var array Configuration that is Updated on Install and Deleted on Uninstallation. */
    // 'preserve' => true to prevent overwrite or delete during install/uninstall process. value is a default.
    // 'global' => true for global context only.
    public static $arrConfigUpdateDeleteList = array(
        // Latest release installed, then preserved until a newer version is installed.
        'TNTOFFICIEL_RELEASE' => array('value' => '', 'global' => true, 'preserve' => true),
    );

    /** @var string[] $arrRemoveFileList List of module files to always delete. */
    public static $arrRemoveFileList = array();

    /** @var string[] $arrRemoveDirList List of module folders to always delete. */
    public static $arrRemoveDirList = array();

    /**
     * Prevent Construct.
     */
    final private function __construct()
    {
        trigger_error(sprintf('%s() %s is static.', __FUNCTION__, get_class($this)), E_USER_ERROR);
    }

    /**
     * Clear Smarty cache.
     *
     * @return bool
     */
    public static function clearCache()
    {
        TNTOfficiel_Logstack::log();

        // Clear Symfony cache.
        // /var/cache/<ENV>/
        Tools::clearSf2Cache();
        // Clear Smarty cache.
        // /var/cache/<ENV>/smarty ?
        Tools::clearSmartyCache();
        // Clear XML cache ('/config/xml/').
        Tools::clearXMLCache();
        // Clear current theme cache.
        // /themes/<THEME>/cache/
        // /themes/<THEME>/assets/cache/
        Media::clearCache();
        // Generate classes index cache.
        // /var/cache/<ENV>/class_index.php
        // /var/cache/<ENV>/class_stub.php
        // /var/cache/<ENV>/namespaced_class_stub.php
        Tools::generateIndex();

        return true;
    }

    /**
     * Remove unused files and unused dirs.
     *
     * @return bool
     */
    public static function uninstallDeprecatedFiles()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficiel_Tools::removeFiles(
            TNTOfficiel::getDirModule(),
            TNTOfficiel_Install::$arrRemoveFileList,
            TNTOfficiel_Install::$arrRemoveDirList
        );
    }

    /**
     * Update settings fields.
     *
     * @return bool
     */
    public static function updateSettings()
    {
        TNTOfficiel_Logstack::log();

        $boolUpdated = true;
        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        foreach (TNTOfficiel_Install::$arrConfigUpdateDeleteList as $strCfgName => $arrConfig) {
            // Must be preserved ?
            $boolPreserve = array_key_exists('preserve', $arrConfig) && $arrConfig['preserve'] === true;
            $boolExist = Configuration::get($strCfgName) !== false;
            // if no need to preserve or not exist.
            if (!$boolPreserve || !$boolExist) {
                // Is global ?
                $boolGlobal = array_key_exists('global', $arrConfig) && $arrConfig['global'] === true;
                // Get value.
                $mxdValue = array_key_exists('value', $arrConfig) ? $arrConfig['value'] : '';

                if ($boolGlobal) {
                    $boolUpdated = $boolUpdated && Configuration::updateGlobalValue($strCfgName, $mxdValue);
                } else {
                    $boolUpdated = $boolUpdated && Configuration::updateValue($strCfgName, $mxdValue);
                }
            }
        }

        TNTOfficiel_Logger::logInstall($strLogMessage, $boolUpdated);

        return $boolUpdated;
    }

    /**
     * Delete settings fields.
     *
     * @return bool
     */
    public static function deleteSettings()
    {
        TNTOfficiel_Logstack::log();

        $boolDeleted = true;
        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        foreach (TNTOfficiel_Install::$arrConfigUpdateDeleteList as $strCfgName => $arrConfig) {
            // Must be preserved ?
            $boolPreserve = array_key_exists('preserve', $arrConfig) && $arrConfig['preserve'] === true;
            if (!$boolPreserve) {
                $boolDeleted = $boolDeleted && Configuration::deleteByName($strCfgName);
            }
        }

        TNTOfficiel_Logger::logUninstall($strLogMessage, $boolDeleted);

        return $boolDeleted;
    }

    /**
     * Creates the tables needed by the module.
     *
     * @return bool
     */
    public static function createTables()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        if (!TNTOfficielCache::createTables()
            || !TNTOfficielAccount::createTables()
            || !TNTOfficielCarrier::createTables()
            || !TNTOfficielCart::createTables()
            || !TNTOfficielOrder::createTables()
            || !TNTOfficielReceiver::createTables()
            || !TNTOfficielParcel::createTables()
            || !TNTOfficielLabel::createTables()
            || !TNTOfficielPickup::createTables()
        ) {
            TNTOfficiel_Logger::logInstall($strLogMessage, false);

            return false;
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return true;
    }

    /**
     * Check that tables and columns exist.
     *
     * @return bool|null true if all columns exist else false. null if undefined.
     */
    public static function checkTables()
    {
        TNTOfficiel_Logstack::log();

        static $boolStaticOnce = false;
        static $boolStaticResult = null;

        if ($boolStaticOnce) {
            return $boolStaticResult;
        }

        $boolStaticOnce = true;
        $boolStaticResult = true;

        if (!TNTOfficielCache::checkTables()
            || !TNTOfficielAccount::checkTables()
            || !TNTOfficielCarrier::checkTables()
            || !TNTOfficielCart::checkTables()
            || !TNTOfficielOrder::checkTables()
            || !TNTOfficielReceiver::checkTables()
            || !TNTOfficielParcel::checkTables()
            || !TNTOfficielLabel::checkTables()
            || !TNTOfficielPickup::checkTables()
        ) {
            $boolStaticResult = false;
        }

        return $boolStaticResult;
    }

    /**
     * Creates the Tab.
     *
     * Tab::getIdFromClassName deprecated since 1.7.1.0.
     *
     * @return bool
     */
    public static function createTab()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $arrLangList = Language::getLanguages(true);

        // Set displayed Tab name for each existing language.
        $arrTabNameLang = array();
        if (is_array($arrLangList)) {
            foreach ($arrLangList as $arrLang) {
                $arrTabNameLang[(int)$arrLang['id_lang']] = TNTOfficiel::MODULE_TITLE;
            }
        }

        // Creates the TNT Orders Tab.
        $objAdminTNTOfficielOrdersTab = new Tab();
        $objAdminTNTOfficielOrdersTab->active = 1;
        $objAdminTNTOfficielOrdersTab->class_name = 'AdminTNTOfficielOrders';
        $objAdminTNTOfficielOrdersTab->name = $arrTabNameLang;
        $objAdminTNTOfficielOrdersTab->module = TNTOfficiel::MODULE_NAME;
        $objAdminTNTOfficielOrdersTab->id_parent = Tab::getIdFromClassName('AdminParentOrders');
        $boolResultAdminTNTOfficielOrdersTab = (bool)$objAdminTNTOfficielOrdersTab->add();

        TNTOfficiel_Logger::logInstall(
            sprintf('%s : %s', $strLogMessage, $objAdminTNTOfficielOrdersTab->class_name),
            $boolResultAdminTNTOfficielOrdersTab
        );

        // Creates the Parent TNT setting Carrier Tab.
        $objAdminTNTOfficielSettingTab = new Tab();
        $objAdminTNTOfficielSettingTab->active = 1;
        $objAdminTNTOfficielSettingTab->class_name = 'AdminTNTOfficielSetting';
        $objAdminTNTOfficielSettingTab->name = $arrTabNameLang;
        $objAdminTNTOfficielSettingTab->module = TNTOfficiel::MODULE_NAME;
        $objAdminTNTOfficielSettingTab->id_parent = Tab::getIdFromClassName('AdminParentShipping');
        $boolResultAdminTNTOfficielSettingTab = (bool)$objAdminTNTOfficielSettingTab->add();

        TNTOfficiel_Logger::logInstall(
            sprintf('%s : %s', $strLogMessage, $objAdminTNTOfficielSettingTab->class_name),
            $boolResultAdminTNTOfficielSettingTab
        );

        // Create the Account setting child Tab (AdminTNTOfficielAccountController).
        $arrAccountSettingTabNameLang = array();
        if (is_array($arrLangList)) {
            foreach ($arrLangList as $arrLang) {
                $arrAccountSettingTabNameLang[(int)$arrLang['id_lang']] = 'Paramétrage du compte marchand';
            }
        }
        $objAdminTNTOfficielAccountTab = new Tab();
        $objAdminTNTOfficielAccountTab->active = 1;
        $objAdminTNTOfficielAccountTab->class_name = 'AdminTNTOfficielAccount';
        $objAdminTNTOfficielAccountTab->name = $arrAccountSettingTabNameLang;
        $objAdminTNTOfficielAccountTab->module = TNTOfficiel::MODULE_NAME;
        $objAdminTNTOfficielAccountTab->id_parent = Tab::getIdFromClassName('AdminTNTOfficielSetting');
        $boolResultAdminTNTOfficielAccountTab = (bool)$objAdminTNTOfficielAccountTab->add();

        TNTOfficiel_Logger::logInstall(
            sprintf('%s : %s', $strLogMessage, $objAdminTNTOfficielAccountTab->class_name),
            $boolResultAdminTNTOfficielAccountTab
        );

        // Create the Carrier setting child Tab (AdminTNTOfficielCarrierController).
        $arrCarrierSettingTabNameLang = array();
        if (is_array($arrLangList)) {
            foreach ($arrLangList as $arrLang) {
                $arrCarrierSettingTabNameLang[(int)$arrLang['id_lang']] = 'Paramétrage des services de livraison TNT';
            }
        }
        $objAdminTNTOfficielCarrierTab = new Tab();
        $objAdminTNTOfficielCarrierTab->active = 1;
        $objAdminTNTOfficielCarrierTab->class_name = 'AdminTNTOfficielCarrier';
        $objAdminTNTOfficielCarrierTab->name = $arrCarrierSettingTabNameLang;
        $objAdminTNTOfficielCarrierTab->module = TNTOfficiel::MODULE_NAME;
        $objAdminTNTOfficielCarrierTab->id_parent = Tab::getIdFromClassName('AdminTNTOfficielSetting');
        $boolResultAdminTNTOfficielCarrierTab = (bool)$objAdminTNTOfficielCarrierTab->add();

        TNTOfficiel_Logger::logInstall(
            sprintf('%s : %s', $strLogMessage, $objAdminTNTOfficielCarrierTab->class_name),
            $boolResultAdminTNTOfficielCarrierTab
        );

        // Create the Status child Tab (AdminTNTOfficielStatusController).
        $arrStatusTabNameLang = array();
        if (is_array($arrLangList)) {
            foreach ($arrLangList as $arrLang) {
                $arrStatusTabNameLang[(int)$arrLang['id_lang']] = 'Status';
            }
        }
        $objAdminTNTOfficielStatusTab = new Tab();
        $objAdminTNTOfficielStatusTab->active = 0;
        $objAdminTNTOfficielStatusTab->class_name = 'AdminTNTOfficielStatus';
        $objAdminTNTOfficielStatusTab->name = $arrStatusTabNameLang;
        $objAdminTNTOfficielStatusTab->module = TNTOfficiel::MODULE_NAME;
        $objAdminTNTOfficielStatusTab->id_parent = Tab::getIdFromClassName('AdminTNTOfficielSetting');
        $boolResultAdminTNTOfficielStatusTab = (bool)$objAdminTNTOfficielStatusTab->add();

        TNTOfficiel_Logger::logInstall(
            sprintf('%s : %s', $strLogMessage, $objAdminTNTOfficielStatusTab->class_name),
            $boolResultAdminTNTOfficielStatusTab
        );

        return ($boolResultAdminTNTOfficielOrdersTab
            && $boolResultAdminTNTOfficielSettingTab
            && $boolResultAdminTNTOfficielAccountTab
            && $boolResultAdminTNTOfficielCarrierTab
            && $boolResultAdminTNTOfficielStatusTab
        );
    }

    /**
     * Delete the Tab.
     *
     * @return bool
     */
    public static function deleteTab()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $objTabsPSCollection = Tab::getCollectionFromModule(TNTOfficiel::MODULE_NAME)->getAll();
        foreach ($objTabsPSCollection as $tab) {
            if (!$tab->delete()) {
                TNTOfficiel_Logger::logUninstall($strLogMessage, false);

                return false;
            }
        }

        TNTOfficiel_Logger::logUninstall($strLogMessage);

        return true;
    }

    /**
     * Create Order Status.
     *
     * @return bool
     */
    public static function createOrderStates()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $arrLangList = Language::getLanguages(true);

        $arrPackageReadyNameLang = array();
        $arrPackageTakenNameLang = array();
        $arrPackageDeliveredPointNameLang = array();
        if (is_array($arrLangList)) {
            foreach ($arrLangList as $arrLang) {
                // Package ready to be handed over to the carrier
                $arrPackageReadyNameLang[(int)$arrLang['id_lang']] =
                    sprintf('Colis prêt à être remis au transporteur [%s]', TNTOfficiel::MODULE_TITLE);
                // Parcel taken in charge by the carrier
                $arrPackageTakenNameLang[(int)$arrLang['id_lang']] =
                    sprintf('Colis pris en charge par le transporteur [%s]', TNTOfficiel::MODULE_TITLE);
                // Delivered to the partner merchant or TNT agency
                $arrPackageDeliveredPointNameLang[(int)$arrLang['id_lang']] =
                    sprintf('Livré chez le commerçant partenaire ou l\'agence TNT [%s]', TNTOfficiel::MODULE_TITLE);
            }
        }

        $arrOrderStateDefault = array(
            // Allow customers to download and read the PDF version of the invoice.
            'invoice' => true,
            // Send an e-mail to the customer when the order status changes.
            'send_email' => false,
            // Associated module name.
            'module_name' => TNTOfficiel::MODULE_NAME,
            // Default color.
            'color' => '#FF00FF',
            // Is unremovable.
            'unremovable' => false,
            // Hide this status in the order for customers.
            'hidden' => false,
            // Consider the associated command as validated.
            'logable' => true,
            // View delivery note PDF.
            'delivery' => true,
            // Mark the associated order as shipped.
            'shipped' => false,
            // Mark the associated order as paid.
            'paid' => true,
            // Attach the PDF invoice to the e-mail.
            'pdf_invoice' => false,
            // Attach the PDF delivery note to the e-mail.
            'pdf_delivery' => false,
            // Name.
            'name' => array(),
            // E-mail template name.
            'template' => '',
            // Logo.
            'logo' => 'preparation.gif',
        );

        $arrOrderStateList = array(
            'TNTOFFICIEL_OS_READYFORPICKUP' => array(
                'logo' => 'preparation.gif',
                'color' => '#00E4F5',
                'shipped' => false,
                'name' => $arrPackageReadyNameLang,
            ),
            'TNTOFFICIEL_OS_TAKENINCHARGE' => array(
                'logo' => 'shipping.gif',
                'color' => '#E099FF',
                'shipped' => true,
                'name' => $arrPackageTakenNameLang,
            ),
            'TNTOFFICIEL_OS_DELIVEREDTOPOINT' => array(
                'logo' => 'delivered.gif',
                'color' => '#10DA97',
                'shipped' => true,
                'name' => $arrPackageDeliveredPointNameLang,
            ),
        );

        foreach ($arrOrderStateList as $strOSConfigName => $arrOrderStateItem) {
            $intOrderStateID = (int)Configuration::get($strOSConfigName);
            $boolOrderStateCreate = !($intOrderStateID > 0);

            // Check OrderState exist.
            if (!$boolOrderStateCreate) {
                $objOrderStateAllDelivered = new OrderState(
                    $intOrderStateID,
                    (int)Configuration::get('PS_LANG_DEFAULT')
                );
                if (!Validate::isLoadedObject($objOrderStateAllDelivered)
                    || (int)$objOrderStateAllDelivered->id !== $intOrderStateID
                ) {
                    $boolOrderStateCreate = true;
                }
            }

            // If not already created.
            if ($boolOrderStateCreate) {
                $objOrderStateItem = (object)array_merge($arrOrderStateDefault, $arrOrderStateItem);

                $objOrderStateNew = new OrderState();
                $objOrderStateNew->invoice = $objOrderStateItem->invoice;
                $objOrderStateNew->send_email = $objOrderStateItem->send_email;
                $objOrderStateNew->module_name = $objOrderStateItem->module_name;
                $objOrderStateNew->color = $objOrderStateItem->color;
                $objOrderStateNew->unremovable = $objOrderStateItem->unremovable;
                $objOrderStateNew->hidden = $objOrderStateItem->hidden;
                $objOrderStateNew->logable = $objOrderStateItem->logable;
                $objOrderStateNew->delivery = $objOrderStateItem->delivery;
                $objOrderStateNew->shipped = $objOrderStateItem->shipped;
                $objOrderStateNew->paid = $objOrderStateItem->paid;
                $objOrderStateNew->name = $objOrderStateItem->name;
                $objOrderStateNew->template = $objOrderStateItem->template;

                // If unable to create new Prestashop OrderState.
                if (!$objOrderStateNew->save()) {
                    TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $strOSConfigName, false);

                    return false;
                }

                // Get new ID.
                $intOrderStateIDNew = (int)$objOrderStateNew->id;

                // Save new OrderState ID in configuration.
                Configuration::updateGlobalValue($strOSConfigName, $intOrderStateIDNew);

                // Add carrier logo.
                $boolResult = copy(
                    TNTOfficiel::getDirModule('image') . 'os/' . $objOrderStateItem->logo,
                    TNTOfficiel::getDirPS() . 'img/os/' . $intOrderStateIDNew . '.gif'
                );
            }

            TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $strOSConfigName);
        }

        return true;
    }

    /**
     * Check that the module is correctly installed.
     *
     * @return bool
     */
    public static function isReady()
    {
        TNTOfficiel_Logstack::log();

        // Check Prestashop version compliance.
        if (version_compare(_PS_VERSION_, TNTOfficiel::getVersionMin(), '<')
            || version_compare(_PS_VERSION_, TNTOfficiel::getVersionMax(), '>')
        ) {
            return false;
        }

        // If module not installed (ps_module:id_module) $this->id > 0
        // or module is downgraded
        // or missing curl/soap essential php extensions.
        if (!(Module::getModuleIdByName(TNTOfficiel::MODULE_NAME) > 0)
            || TNTOfficiel::isDownGraded()
            || !extension_loaded('curl')
            || !extension_loaded('soap')
        ) {
            return false;
        }

        // Check that tables and columns exist.
        if (!TNTOfficiel_Install::checkTables()) {
            return false;
        }

        return true;
    }
}
