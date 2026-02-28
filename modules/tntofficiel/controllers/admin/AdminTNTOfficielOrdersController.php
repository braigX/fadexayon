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
 * Class AdminTNTOfficielOrdersController
 */
class AdminTNTOfficielOrdersController extends ModuleAdminController
{
    public $toolbar_title;
    protected $statuses_array = array();

    public function __construct()
    {
        TNTOfficiel_Logstack::log();

        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = false;
        $this->deleted = false;

        parent::__construct();

        $this->_select = '
        a.id_currency,
        a.id_order AS id_pdf,
        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
        osl.`name` AS `osname`,
        os.`color`,
        -- override start.
        `tl`.`label_name` as `BT`,
        `a`.`id_order` as `tntofficiel_id_order`,
        `to`.`pickup_number` as `tntofficiel_pickup_number`,
        c1.`name` AS `carrier`,
        c1.`id_carrier` AS id_carrier,
        -- override end.
        IF(a.valid, 1, 0) badge_success';

        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
        LEFT JOIN `' . _DB_PREFIX_ . 'address` address ON address.id_address = a.id_address_delivery
        LEFT JOIN `' . _DB_PREFIX_ . 'country` country ON address.id_country = country.id_country
        LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang
            ON (country.`id_country` = country_lang.`id_country`
                AND country_lang.`id_lang` = ' . (int)$this->context->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`)
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
            ON (os.`id_order_state` = osl.`id_order_state`
                AND osl.`id_lang` = ' . (int)$this->context->language->id . ')
        -- override start.
        JOIN `' . _DB_PREFIX_ . 'carrier` c1 ON (a.`id_carrier` = c1.`id_carrier` AND  c1.`external_module_name` = "'
            . pSQL(TNTOfficiel::MODULE_NAME) . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'tntofficiel_order` `to` ON (a.`id_order` = `to`.`id_order`)
        LEFT JOIN `' . _DB_PREFIX_ . 'tntofficiel_label` `tl` ON (a.`id_order` = `tl`.`id_order`)
        -- override end.
        ';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $carriers = Carrier::getCarriers(
            (int)$this->context->language->id,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );
        $carriers_array = array();
        foreach ($carriers as $carrier) {
            // If is a TNT carrier.
            if (TNTOfficielCarrier::isTNTOfficielCarrierID($carrier['id_carrier'])) {
                $carriers_array[$carrier['id_carrier']] = $carrier['name'];
            }
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldIDStr'),
                // Explicit table name reference.
                'filter_key' => 'a!id_order',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'reference' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldReferenceStr'),
                // Explicit table name reference.
                'filter_key' => 'a!reference',
            ),
            'customer' => array(
                'title' => TNTOfficiel::getCodeTranslate('fieldCustomerStr'),
                'havingFilter' => true,
            ),
        );
        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge(
                $this->fields_list,
                array(
                    'company' => array(
                        'title' => TNTOfficiel::getCodeTranslate('fieldCompanyStr'),
                        // Table name reference.
                        'filter_key' => 'c!company',
                    ),
                )
            );
        }

        $this->fields_list = array_merge(
            $this->fields_list,
            array(
                // Added : Carrier select.
                'carrier' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldCarrierStr'),
                    // Table name reference.
                    'filter_key' => 'c1!id_carrier',
                    'filter_type' => 'int',
                    'order_key' => 'carrier',
                    'havingFilter' => true,
                    'type' => 'select',
                    // Mapping list for select.
                    'list' => $carriers_array,
                ),
                'total_paid_tax_incl' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldTotalStr'),
                    // Cell content using callback.
                    'callback' => 'setOrderCurrency',
                    'type' => 'price',
                    'currency' => true,
                    // Badged price if valid (see badge_success in _select).
                    'badge_success' => true,
                    'align' => 'text-right',
                ),
                'payment' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldPaymentStr'),
                    // Explicit table name reference.
                    'filter_key' => 'a!payment',
                ),
                'osname' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldStatusStr'),
                    // Table name reference.
                    'filter_key' => 'os!id_order_state',
                    'filter_type' => 'int',
                    'order_key' => 'osname',
                    'type' => 'select',
                    // Mapping list for select.
                    'list' => $this->statuses_array,
                    // Use colored label.
                    'color' => 'color',
                ),
                'date_add' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldDateStr'),
                    // Explicit table name reference.
                    'filter_key' => 'a!date_add',
                    'type' => 'datetime',
                    'align' => 'text-right',
                ),
                'id_pdf' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldPDFStr'),
                    // Cell content using callback.
                    'callback' => 'printPDFIcons',
                    // Disable table header sort.
                    'orderby' => false,
                    // Disable table header search.
                    'search' => false,
                    // No td onclick event (buttons inside).
                    'remove_onclick' => true,
                    'align' => 'text-center',
                ),
                // Added after 'id_pdf': TNT BT.
                'tntofficiel_id_order' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldTNTStr'),
                    // Cell content using callback.
                    'callback' => 'printBtIcon',
                    // Disable table header sort.
                    'orderby' => false,
                    // Disable table header search.
                    'search' => false,
                    // No td onclick event (buttons inside).
                    'remove_onclick' => true,
                    'align' => 'text-center',
                ),
            )
        );

        $this->fields_list = array_merge(
            $this->fields_list,
            array(
                // Optional TNT Pickup Number.
                // Filed removed later in getList() if unused.
                'tntofficiel_pickup_number' => array(
                    'title' => TNTOfficiel::getCodeTranslate('fieldPickupNumStr'),
                    // Cell content using callback.
                    'callback' => 'printPickUpNumber',
                    // Disable table header sort.
                    'orderby' => false,
                    // Disable table header search.
                    'search' => false,
                    'align' => 'text-right',
                ),
            )
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        // For all request concerning an id_order (incl. AJAX).
        if (Tools::getIsset('id_order')) {
            // Set context cart and customer.
            $objPSOrder = TNTOfficielOrder::getPSOrderByID((int)Tools::getValue('id_order'));
            $this->context->cart = TNTOfficielCart::getPSCartByID($objPSOrder->id_cart);
            $this->context->customer = TNTOfficielReceiver::getPSCustomerByID($objPSOrder->id_customer);
        }

        $this->bulk_actions = array();

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If an account is available for current shop context.
        if ($objTNTContextAccountModel !== null) {
            if (Shop::getContext() === Shop::CONTEXT_SHOP) {
                $intLangID = (int)$this->context->language->id;
                $objOrderStateShipmentSave = $objTNTContextAccountModel->getOSShipmentSave($intLangID);
                if ($objOrderStateShipmentSave !== null) {
                    $this->bulk_actions += array(
                        // Apply.
                        'updateOrderStatus' => array(
                            'text' => sprintf(
                                TNTOfficiel::getCodeTranslate('bulkApplyStatusStr'),
                                $objOrderStateShipmentSave->name
                            ),
                            'icon' => 'icon-time',
                        ),
                    );
                }
            }
        }

        $this->bulk_actions += array(
            // TNT BT.
            'getBT' => array(
                'text' => TNTOfficiel::getCodeTranslate('bulkShippingLabelStr'),
                'icon' => 'icon-tnt',
            ),
            // TNT Manifest.
            'getManifest' => array(
                'text' => TNTOfficiel::getCodeTranslate('bulkManifestStr'),
                'icon' => 'icon-file-text',
            ),
            // Update order status for all parcels delivered.
            'updateDelivered' => array(
                // $objOrderStateAllDelivered->name
                'text' => TNTOfficiel::getCodeTranslate('bulkRefreshDeliveryStatusStr'),
                'icon' => 'icon-refresh',
            ),
        );
    }

    /**
     * Load script.
     */
    public function setMedia($isNewTheme = false)
    {
        TNTOfficiel_Logstack::log();

        parent::setMedia(false);

        // Get Order.
        $intOrderIDView = Tools::getValue('vieworder');
        // No order to view : Order list.
        if ($intOrderIDView === false) {
            $this->module->addJS('AdminTNTOfficielOrders.js');
        }
    }

    /**
     * Get the current objects' list form the database
     *
     * @param int         $id_lang   Language used for display
     * @param string|null $order_by  ORDER BY clause
     * @param string|null $order_way Order way (ASC, DESC)
     * @param int         $start     Offset in LIMIT clause
     * @param int|null    $limit     Row count in LIMIT clause
     * @param int|bool    $id_lang_shop
     */
    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        TNTOfficiel_Logstack::log();

        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        $boolDisplayPickupNumberColumn = false;

        foreach ($this->_list as $arrRow) {
            $intCarrierID = (int)$arrRow['id_carrier'];

            $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
            if ($objTNTCarrierModel === null) {
                continue;
            }

            $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();
            // If no account available for this carrier.
            if ($objTNTCarrierAccountModel === null) {
                continue;
            }

            $boolDisplayPickupNumberColumn = $boolDisplayPickupNumberColumn
                || ($objTNTCarrierAccountModel->pickup_display_number ? true : false);
        }

        // If no need to display.
        if (!$boolDisplayPickupNumberColumn) {
            // Remove column.
            unset($this->fields_list['tntofficiel_pickup_number']);
        }
    }

    /**
     * Set Header with breadcrumbs.
     */
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

        //this->meta_title
        //$this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
        //$this->page_header_toolbar_title = $this->toolbar_title[count($this->toolbar_title) - 1];
        $this->page_header_toolbar_btn = array();

        $this->show_page_header_toolbar = true;

        parent::initPageHeaderToolbar();

        // Remove Help.
        $this->context->smarty->assign(
            array(
                'help_link' => null,
            )
        );
    }

    /**
     * Retrieve GET and POST value and translate them to actions.
     */
    public function initProcess()
    {
        TNTOfficiel_Logstack::log();

        parent::initProcess();

        if ($this->display === null) {
            // renderList() : null,
            // 'reset_filters',
            // Process Bulk : 'bulkupdateOrderStatus', 'bulkgetBT', ...
            // Process Column : 'getManifest', ...
            //$this->action;

            return;
        }

        if ($this->display === 'view'
            && $this->action === 'view'
        ) {
            // renderView()
            return;
        }

        $this->display = null;
        $this->action = null;
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     */
    public function initToolbar()
    {
        TNTOfficiel_Logstack::log();

        // No toolbar (upper right).
        $this->toolbar_btn = array();
    }

    /**
     * Function used to render the list to display for this controller.
     *
     * @return string|false
     *
     * @throws PrestaShopException
     */
    public function renderList()
    {
        TNTOfficiel_Logstack::log();

        if (Tools::getIsset('submitBulkupdateOrderStatus' . $this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }

            // <ADMIN>/themes/default/template/controllers/orders/helpers/list/list_header.tpl
            $this->tpl_list_vars['updateOrderStatus_mode'] = true;
            $this->tpl_list_vars['order_statuses'] = $this->statuses_array;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['POST'] = $_POST;
        }

        return parent::renderList();
    }

    /**
     * View redirect on AdminOrders order or AdminTNTOfficielOrders list (PS1.7.7-).
     *
     * @return string
     */
    public function renderView()
    {
        TNTOfficiel_Logstack::log();

        // Get Order.
        $intOrderIDView = Tools::getValue('vieworder');
        // No order to view : Order list.
        if ($intOrderIDView === false) {
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminTNTOfficielOrders')
            );
        } else {
            // Redirect from AdminTNTOfficielOrders to AdminOrders page.
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'AdminOrders',
                    true,
                    array(),
                    array(
                        'id_order' => Tools::getValue('id_order'),
                        'vieworder' => 1,
                    )
                )
            );
        }

        return '';
    }

    /**
     * @param $echo
     * @param $tr
     *
     * @return mixed
     */
    public static function setOrderCurrency($echo, $tr)
    {
        TNTOfficiel_Logstack::log();

        $objPSOrder = TNTOfficielOrder::getPSOrderByID((int)$tr['id_order']);
        // If Order object not available.
        if ($objPSOrder === null) {
            return '';
        }

        return Tools::displayPrice($echo, (int)$objPSOrder->id_currency);
    }

    /**
     * @param $id_order
     * @param $tr
     *
     * @return string|null
     */
    public function printPDFIcons($id_order, $tr)
    {
        TNTOfficiel_Logstack::log();

        static $valid_order_state = array();

        $intOrderID = (int)$id_order;

        $objPSOrder = TNTOfficielOrder::getPSOrderByID($intOrderID);
        // If Order object not available.
        if ($objPSOrder === null) {
            return '';
        }

        if (!isset($valid_order_state[$objPSOrder->current_state])) {
            $valid_order_state[$objPSOrder->current_state] =
                Validate::isLoadedObject($objPSOrder->getCurrentOrderState());
        }

        if (!$valid_order_state[$objPSOrder->current_state]) {
            return '';
        }

        $this->context->smarty->assign(
            array(
                'objPSOrder' => $objPSOrder,
                'hrefGenerateInvoicePDF' => $this->context->link->getAdminLink(
                    'AdminPdf',
                    true,
                    array(),
                    array(
                        'submitAction' => 'generateInvoicePDF',
                        'id_order' => $intOrderID,
                    )
                ),
                'hrefGenerateDeliverySlipPDF' => $this->context->link->getAdminLink(
                    'AdminPdf',
                    true,
                    array(),
                    array(
                        'submitAction' => 'generateDeliverySlipPDF',
                        'id_order' => $intOrderID,
                    )
                ),
                'tr' => $tr,
            )
        );

        return $this->context->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/_print_pdf_icon.tpl'
        );
    }

    /***
     * @param $id_order
     * @param $tr
     *
     * @return string|null
     */
    public function printBtIcon($id_order, $tr)
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)$id_order;

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            return null;
        }

        $strBTLabelName = '';
        if ($objTNTOrderModel->isExpeditionCreated()) {
            // Load an existing TNT label info.
            $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intOrderID, false);
            // If success.
            if ($objTNTLabelModel !== null) {
                $strBTLabelName = $objTNTLabelModel->getLabelName();
            }
        }

        $this->context->smarty->assign(
            array(
                'hrefDownloadBT' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'downloadBT',
                        'id_order' => $intOrderID,
                    )
                ),
                'hrefGetManifest' => $this->context->link->getAdminLink(
                    'AdminTNTOfficielOrders',
                    true,
                    array(),
                    array(
                        'action' => 'getManifest',
                        'id_order' => $intOrderID,
                    )
                ),
                'strBTLabelName' => $strBTLabelName,
                'tr' => $tr,
            )
        );

        return $this->context->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/_print_bt_icon.tpl'
        );
    }

    /**
     * @param $pickup_number
     * @param $tr
     *
     * @return string|null
     */
    public function printPickUpNumber($pickup_number, $tr)
    {
        TNTOfficiel_Logstack::log();

        $intCarrierID = (int)$tr['id_carrier'];

        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
        if ($objTNTCarrierModel === null) {
            return null;
        }

        $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();
        // If no account available for this carrier.
        if ($objTNTCarrierAccountModel === null) {
            return null;
        }

        return ($objTNTCarrierAccountModel->pickup_display_number ? $pickup_number : null);
    }

    /**
     * Downloads an archive containing all the logs files.
     * /<ADMIN>/index.php?controller=AdminTNTOfficielOrders&action=downloadLogs
     * /modules/tntofficiel/log/logs.zip
     */
    public function processDownloadLogs()
    {
        TNTOfficiel_Logstack::log();

        // Create Zip.
        $strZipContent = TNTOfficiel_Tools::getZip(
            TNTOfficiel_Logstack::getRootPath(),
            array('log', 'json')
        );

        // Download and exit.
        TNTOfficiel_Tools::download('log.zip', $strZipContent);

        // We want to be sure that downloading is the last thing this controller will do.
        exit;
    }

    /**
     * Apply OrderState for shipment creation.
     */
    public function processBulkUpdateOrderStatus()
    {
        TNTOfficiel_Logstack::log();

        $objCookie = $this->context->cookie;

        $arrOrderID = array();
        if (Tools::getIsset('orderBox')) {
            $arrOrderID = (array)Tools::getValue('orderBox');
        }

        if (count($arrOrderID) === 0) {
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);

            return;
        }

        $arrErrorMessageList = array();

        foreach ($arrOrderID as $strOrderID) {
            $intOrderID = (int)$strOrderID;
            // Load TNT order info for its ID.
            $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
            // If fail.
            if ($objTNTOrderModel === null) {
                $arrErrorMessageList[] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorUnableLoadIDStr'),
                    'TNTOfficielOrder',
                    $intOrderID
                );
                // Next.
                continue;
            }

            // If already created.
            if ($objTNTOrderModel->isExpeditionCreated()) {
                // Next.
                continue;
            }

            $objOrderStateShipmentSave = $objTNTOrderModel->getOSShipmentSave();
            // If no OrderStatus available for this order carrier account.
            if ($objOrderStateShipmentSave === null) {
                // Next.
                continue;
            }

            $boolUpdatedOS = $objTNTOrderModel->addOrderStateHistory(
                (int)$objOrderStateShipmentSave->id,
                (int)$this->context->employee->id
            );

            if ($boolUpdatedOS === false) {
                $arrErrorMessageList[] = sprintf(
                    TNTOfficiel::getCodeTranslate('errorUnableApplyOSForOrderIDStr'),
                    $objOrderStateShipmentSave->name,
                    $intOrderID
                );
            }
        }

        if (count($arrErrorMessageList) > 0) {
            $objCookie->TNTOfficielError = implode("\n", $arrErrorMessageList);
        } else {
            $objCookie->TNTOfficielSuccess = TNTOfficiel::getCodeTranslate('successUpdateSuccessful');
        }

        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    /**
     * Concatenate PDF for all the BT for the selected orders.
     *
     * @throws Exception
     */
    public function processBulkGetBT()
    {
        TNTOfficiel_Logstack::log();

        $arrOrderID = (array)Tools::getValue('orderBox');
        $objPDFMerger = new TNTOfficiel_PDFMerger();
        $intBTCounter = 0;

        foreach ($arrOrderID as $strOrderID) {
            $intOrderID = (int)$strOrderID;
            // If not an order associated with a TNT Carrier.
            if (!TNTOfficielOrder::isTNTOfficielOrderID($intOrderID)) {
                // Skip.
                continue;
            }
            // Load TNT order info for its ID.
            $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
            // If fail or expedition is not created.
            if ($objTNTOrderModel === null || !$objTNTOrderModel->isExpeditionCreated()) {
                continue;
            }
            // Load an existing TNT label info.
            $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intOrderID, false);
            // If fail.
            if ($objTNTLabelModel === null) {
                continue;
            }

            $strBTLabelName = $objTNTLabelModel->getLabelName();
            $strLabelPDFContent = $objTNTLabelModel->getLabelPDFContent();

            if (Tools::strlen($strBTLabelName) > 0 && Tools::strlen($strLabelPDFContent) > 0) {
                ++$intBTCounter;
                // Merge pdf BT content.
                $objPDFMerger->addPDF($strBTLabelName, 'all', $strLabelPDFContent);
            }
        }

        // Concat.
        if ($intBTCounter > 0) {
            $strOutputFileName = 'bt_list.pdf';
            // Download and exit.
            TNTOfficiel_Tools::download(
                $strOutputFileName,
                $objPDFMerger->merge('string', $strOutputFileName),
                'application/pdf'
            );
        }
    }

    /**
     * Return all the Manifest for the selected orders.
     */
    public function processBulkGetManifest()
    {
        TNTOfficiel_Logstack::log();

        if (!Tools::getIsset('orderBox')) {
            return;
        }

        $arrOrderID = (array)Tools::getValue('orderBox');
        $arrOrderIDList = array();
        foreach ($arrOrderID as $strOrderID) {
            $intOrderID = (int)$strOrderID;
            $arrOrderIDList[] = $intOrderID;
        }

        TNTOfficiel_PDFCreator::createManifest($arrOrderIDList);
    }

    /**
     * Update order parcels tracking state and delivered orderstate accordingly.
     */
    public function processBulkUpdateDelivered()
    {
        TNTOfficiel_Logstack::log();

        $objCookie = $this->context->cookie;

        $arrOrderID = array();
        if (Tools::getIsset('orderBox')) {
            $arrOrderID = (array)Tools::getValue('orderBox');
        }

        if (count($arrOrderID) === 0) {
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);

            return;
        }

        foreach ($arrOrderID as $intOrderID) {
            $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
            if ($objTNTOrderModel !== null) {
                // Update parcel tracking state and order state accordingly.
                $objTNTOrderModel->updateOrderStateDeliveredParcels();
            }
        }

        $objCookie->TNTOfficielSuccess = TNTOfficiel::getCodeTranslate('successUpdateSuccessful');
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    /**
     *
     */
    public function processDownloadBT()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');

        // Load an existing TNT label info.
        $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intOrderID, false);
        // If fail.
        if ($objTNTLabelModel === null) {
            return;
        }

        $strBTLabelName = $objTNTLabelModel->getLabelName();
        $strLabelPDFContent = $objTNTLabelModel->getLabelPDFContent();

        // Download and exit.
        if (Tools::strlen($strBTLabelName) > 0 && Tools::strlen($strLabelPDFContent) > 0) {
            TNTOfficiel_Tools::download($strBTLabelName, $strLabelPDFContent, 'application/pdf');
        }

        // We want to be sure that downloading is the last thing this controller will do.
        exit;
    }

    /**
     * Generate the manifest for an order (download).
     */
    public function processGetManifest()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');
        $arrOrderIDList = array($intOrderID);

        TNTOfficiel_PDFCreator::createManifest($arrOrderIDList);

        // We want to be sure that downloading is the last thing this controller will do.
        exit;
    }

    /**
     * @return bool
     */
    public function displayAjaxSelectPostcodeCities()
    {
        TNTOfficiel_Logstack::log();

        $strCountryISO = 'FR';

        // Check the country
        $strZipCode = pSQL(Tools::getValue('zipcode'));
        $strCity = pSQL(Tools::getValue('city'));

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return false;
        }

        // Check the city/postcode.
        $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide($strCountryISO, $strZipCode, $strCity);

        echo TNTOfficiel_Tools::encJSON($arrResultCitiesGuide);

        return true;
    }

    /**
     * Get cities for a postcode.
     *
     * @return bool false on failure. true on success.
     */
    public function displayAjaxGetCities()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');

        $arrResult = array(
            'valid' => true,
            'cities' => array(),
            'postcode' => false,
        );

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $objPSAddressDelivery = $objTNTOrderModel->getPSAddressDelivery();
        // If delivery address object is not available.
        if ($objPSAddressDelivery === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objPSAddressDelivery->id_country);
        $strReceiverZipCode = trim($objPSAddressDelivery->postcode);
        $strReceiverCity = trim($objPSAddressDelivery->city);

        // Check the city/postcode.
        $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide(
            $strReceiverCountryISO,
            $strReceiverZipCode,
            $strReceiverCity
        );

        $arrResult = array(
            // Is current ZipCode/CityName Valid for FR (else valid) ?
            // Unsupported country or communication error is considered true to prevent always
            // invalid address form and show error "unknow postcode" on Front-Office checkout.
            'valid' => (!$arrResultCitiesGuide['boolIsCountrySupported']
                || $arrResultCitiesGuide['boolIsRequestComError']
                || $arrResultCitiesGuide['boolIsCityNameValid']
            ),
            // Cities name list available for current ZipCode.
            'cities' => $arrResultCitiesGuide['arrCitiesNameList'],
            // Current ZipCode.
            'postcode' => $arrResultCitiesGuide['strZipCode'],
        );

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Update the city for the current delivery address.
     *
     * @return bool
     */
    public function displayAjaxUpdateDeliveryAddress()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');
        $strCity = trim(pSQL(Tools::getValue('city')));

        $arrResult = array(
            'result' => false,
        );

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $objPSAddressDelivery = $objTNTOrderModel->getPSAddressDelivery();
        // If delivery address object is not available.
        if ($objPSAddressDelivery === null) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        // If not a string or zero length.
        if (!is_string($strCity) || !(Tools::strlen($strCity) > 0)) {
            echo TNTOfficiel_Tools::encJSON($arrResult);

            return false;
        }

        $objPSAddressDelivery->city = $strCity;
        $arrResult['result'] = $objPSAddressDelivery->save();

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Check if the city match the postcode.
     *
     * @return bool
     */
    public function displayAjaxCheckPostcodeCity()
    {
        TNTOfficiel_Logstack::log();

        $arrResult = array(
            'required' => false,
            'postcode' => false,
            'cities' => false,
        );

        // Check the country
        $intCountryID = (int)Tools::getValue('countryId');
        $strCountryISO = TNTOfficielReceiver::getCountryISOCode($intCountryID);
        $strZipCode = pSQL(Tools::getValue('postcode'));
        $strCity = pSQL(Tools::getValue('city'));

        // Get account for current shop context (or create it from inherit).
        $objTNTContextAccountModel = TNTOfficielAccount::loadContextShop();
        // If no account available for current shop context.
        if ($objTNTContextAccountModel === null) {
            return false;
        }

        if ($strCountryISO === 'FR') {
            // Check is required for France.
            $arrResult['required'] = true;
            // Check the city/postcode.
            $arrResultCitiesGuide = $objTNTContextAccountModel->citiesGuide($strCountryISO, $strZipCode, $strCity);
            // PostCode is well formated NNNNN
            if ($arrResultCitiesGuide['strZipCode'] !== null) {
                // If city/postcode correct.
                // If communication error, TNT carrier are not available,
                // but postcode/city is considered wrong and then show error "unknow postcode" on Front-Office checkout.
                // Also, return true to prevent always invalid address form.
                if ($arrResultCitiesGuide['boolIsRequestComError'] || $arrResultCitiesGuide['boolIsCityNameValid']) {
                    $arrResult['postcode'] = true;
                    $arrResult['cities'] = true;
                } else {
                    // Get cities from the webservice from the given postal code.
                    if (count($arrResultCitiesGuide['arrCitiesNameList']) > 0) {
                        $arrResult['postcode'] = true;
                    }

                    $arrResult['cities'] = $arrResultCitiesGuide['arrCitiesNameList'];
                }
            }
        }

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Get the delivery points popup via Ajax.
     * DROPOFFPOINT (Commerçants Partenaires) : XETT
     * DEPOT (Agences TNT) : PEX
     *
     * @return bool
     */
    public function displayAjaxBoxDeliveryPoints()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            return false;
        }

        // Get the selected TNT Carrier object from Order.
        $objTNTCarrierModel = $objTNTOrderModel->getTNTCarrierModel();
        if ($objTNTCarrierModel === null) {
            return false;
        }

        $strArgZipCode = trim(pSQL(Tools::getValue('tnt_postcode')));
        $strArgCity = trim(pSQL(Tools::getValue('tnt_city')));

        $objPSAddressDelivery = $objTNTOrderModel->getPSAddressDelivery();
        // Default from delivery address.
        if (!$strArgZipCode && !$strArgCity && $objPSAddressDelivery !== null) {
            $strArgZipCode = trim($objPSAddressDelivery->postcode);
            $strArgCity = trim($objPSAddressDelivery->city);
        }

        $arrResultDeliveryPoints = $objTNTCarrierModel->getDeliveryPoints($strArgZipCode, $strArgCity);
        if ($arrResultDeliveryPoints === null) {
            return false;
        }

        // Get the relay points
        $this->context->smarty->assign(
            array(
                'carrier_type' => $objTNTCarrierModel->carrier_type,
                'current_postcode' => $arrResultDeliveryPoints['strZipCode'],
                'current_city' => $arrResultDeliveryPoints['strCity'],
                'arrRespositoryList' => $arrResultDeliveryPoints['arrPointsList'],
                'cities' => $arrResultDeliveryPoints['arrCitiesNameList'],
            )
        );

        echo $this->context->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'front/displayAjaxBoxDeliveryPoints.tpl'
        );

        return true;
    }

    /**
     * Save delivery point info for order.
     *
     * @return bool
     */
    public function displayAjaxSaveProductInfo()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('id_order');
        $strDeliveryPoint = (string)Tools::getValue('product');

        $strDeliveryPointJSON = TNTOfficiel_Tools::inflate($strDeliveryPoint);
        $arrDeliveryPoint = TNTOfficiel_Tools::decJSON($strDeliveryPointJSON);

        // Load TNT order info or create a new one for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => sprintf(
                        TNTOfficiel::getCodeTranslate('errorUnableLoadIDStr'),
                        'TNTOfficielOrder',
                        $intOrderID
                    ),
                )
            );

            return false;
        }

        if ($objTNTOrderModel->setDeliveryPoint($arrDeliveryPoint) === false) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => TNTOfficiel::getCodeTranslate('errorUnableSetDeliveryPointStr'),
                )
            );

            return false;
        }

        // Save TNT order.
        $boolResult = $objTNTOrderModel->save();
        // If fail.
        if (!$boolResult) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => sprintf(
                        TNTOfficiel::getCodeTranslate('errorUnableSaveIDStr'),
                        'TNTOfficielOrder',
                        $intOrderID
                    ),
                )
            );

            return false;
        }

        // Reload.
        echo TNTOfficiel_Tools::encJSON(
            array(
                'reload' => true,
            )
        );

        return true;
    }

    /**
     * Store Extra Information of Receiver Delivery Address (BO).
     *
     * @return bool
     */
    public function displayAjaxStoreReceiverInfo()
    {
        TNTOfficiel_Logstack::log();

        // Default is technical Error.
        $arrFormReceiverInfoValidate = array(
            'fields' => array(),
            'errors' => array(),
            'length' => 0,
            'stored' => false,
        );

        $intOrderID = (int)Tools::getValue('id_order');

        $objPSOrder = TNTOfficielOrder::getPSOrderByID($intOrderID);
        // If Order object not available.
        if ($objPSOrder === null) {
            echo TNTOfficiel_Tools::encJSON($arrFormReceiverInfoValidate);

            return false;
        }

        // Load TNT receiver info or create a new one for its ID.
        $objTNTReceiverModel = TNTOfficielReceiver::loadAddressID($objPSOrder->id_address_delivery);
        // If fail.
        if ($objTNTReceiverModel === null) {
            echo TNTOfficiel_Tools::encJSON($arrFormReceiverInfoValidate);

            return false;
        }

        // Validate and store receiver info, using form values.
        $arrFormReceiverInfoValidate = $objTNTReceiverModel->storeReceiverInfo(
            (string)Tools::getValue('receiver_email'),
            (string)Tools::getValue('receiver_phone'),
            (string)Tools::getValue('receiver_building'),
            (string)Tools::getValue('receiver_accesscode'),
            (string)Tools::getValue('receiver_floor'),
            (string)Tools::getValue('receiver_instructions')
        );

        echo TNTOfficiel_Tools::encJSON($arrFormReceiverInfoValidate);

        return true;
    }

    /**
     * Display the tracking popup.
     *
     * @return bool
     */
    public function displayAjaxTracking()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('orderId');

        // Load TNT order.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel !== null) {
            // Update tracking state.
            $objTNTOrderModel->updateParcelsTrackingState();
            // Get parcels.
            $arrObjTNTParcelModelList = $objTNTOrderModel->getTNTParcelModelList();
            if ((count($arrObjTNTParcelModelList) > 0)) {
                $this->context->smarty->assign(
                    array(
                        'arrObjTNTParcelModelList' => $arrObjTNTParcelModelList,
                    )
                );
                echo $this->context->smarty->fetch(
                    TNTOfficiel::getDirModule('template') . 'front/displayAjaxTracking.tpl'
                );

                return true;
            }
        }

        // 404 fallback.
        Controller::getController('AdminNotFoundController')->run();

        return false;
    }

    /**
     * Update parcels.
     *
     * @param TNTOfficielOrder $objTNTOrderModel
     *
     * @return array
     */
    // TODO: replace by std ajaxProcessUpdateOrderParcels ?
    public function processAjaxUpdateOrderParcels($objTNTOrderModel)
    {
        $intOrderID = $objTNTOrderModel->id_order;

        // Delete, Update or Create a parcel.
        if (Tools::getIsset('parcelId')) {
            $intParcelID = (int)Tools::getValue('parcelId');

            // Non Editable.
            if ($objTNTOrderModel->isExpeditionCreated()) {
                return array(
                    'error' => sprintf(TNTOfficiel::getCodeTranslate('errorAlreadyShipped')),
                );
            }

            // Get the parcels list.
            $arrObjTNTParcelModelList = $objTNTOrderModel->getTNTParcelModelList();

            // 0 to Create a parcel, else ID to Delete or Update a parcel.
            if ($intParcelID === 0) {
                // At max 30 parcels allowed.
                if (count($arrObjTNTParcelModelList) >= 30) {
                    return array(
                        'error' => sprintf(TNTOfficiel::getCodeTranslate('errorAtMostParcels')),
                    );
                }

                $objTNTParcelModel = TNTOfficielParcel::loadParcelID();
                if ($objTNTParcelModel === null) {
                    return array(
                        'error' => sprintf(TNTOfficiel::getCodeTranslate('errorUnableCreateStr'), 'TNTOfficielParcel'),
                    );
                }

                $boolResult = $objTNTParcelModel->setOrderID($intOrderID);
                if (is_string($boolResult)) {
                    return array(
                        'error' => $boolResult,
                    );
                }

                // Set default weight.
                $boolResult = $objTNTParcelModel->setWeight(TNTOfficielParcel::MIN_WEIGHT);
                if (is_string($boolResult)) {
                    return array(
                        'error' => $boolResult,
                    );
                }
            } elseif ($intParcelID > 0) {
                // Check parcel belong to order.
                if (!array_key_exists($intParcelID, $arrObjTNTParcelModelList)) {
                    return array(
                        'error' => sprintf(
                            TNTOfficiel::getCodeTranslate('errorUnableLoadIDStr'),
                            'TNTOfficielParcel',
                            $intParcelID
                        ),
                    );
                }

                /** @var TNTOfficielParcel $objTNTParcelModel */
                $objTNTParcelModel = $arrObjTNTParcelModelList[$intParcelID];

                $strWeight = (string)Tools::getValue('weight');
                // If does not represents a number.
                if ($strWeight !== (string)(float)$strWeight) {
                    return array(
                        'error' => TNTOfficiel::getCodeTranslate('errorInvalidWeight'),
                        'id' => 'parcelWeight-'.$objTNTParcelModel->id,
                    );
                }

                $fltWeight = (float)$strWeight;

                // Delete parcel.
                if ($fltWeight === 0.0) {
                    // At least one parcel required.
                    if (count($arrObjTNTParcelModelList) <= 1) {
                        return array(
                            'error' => TNTOfficiel::getCodeTranslate('errorAtLeastParcel'),
                        );
                    }

                    $boolSuccess = $objTNTParcelModel->delete();
                    if (!$boolSuccess) {
                        return array(
                            'error' => TNTOfficiel::getCodeTranslate('errorTechnical'),
                        );
                    }
                } else {
                    $boolResult = $objTNTParcelModel->setWeight($fltWeight);
                    if (is_string($boolResult)) {
                        return array(
                            'error' => $boolResult,
                            'id' => 'parcelWeight-'.$objTNTParcelModel->id,
                        );
                    }

                    $fltInsuranceAmount = null;
                    if (Tools::getIsset('parcelInsuranceAmount')) {
                        $strInsuranceAmount = (string)Tools::getValue('parcelInsuranceAmount');
                        // If does not represents a number.
                        if ($strInsuranceAmount !== (string)(float)$strInsuranceAmount) {
                            return array(
                                'error' => TNTOfficiel::getCodeTranslate('errorInvalidAmount'),
                                'id' => 'parcelInsuranceAmount-'.$objTNTParcelModel->id,
                            );
                        }

                        $fltInsuranceAmount = (float)$strInsuranceAmount;

                        $boolResult = $objTNTParcelModel->setInsuranceAmount($fltInsuranceAmount);
                        if (is_string($boolResult)) {
                            return array(
                                'error' => $boolResult,
                                'id' => 'parcelInsuranceAmount-'.$objTNTParcelModel->id,
                            );
                        }
                    }
                }
            }
        }

        // All parcels.
        $boolDelivered = $objTNTOrderModel->updateOrderStateDeliveredParcels(10 * 60);

        return array(
            // If all parcels delivered and order state delivered is applied.
            'reload' => ($boolDelivered === true),
        );
    }

    /**
     * Update parcels.
     *
     * @return bool
     */
    public function displayAjaxUpdateOrderParcels()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('orderId');

        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        if ($objTNTOrderModel === null) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => sprintf(
                        TNTOfficiel::getCodeTranslate('errorUnableLoadIDStr'),
                        'TNTOfficielOrder',
                        $intOrderID
                    ),
                )
            );

            return false;
        }

        $objTNTCarrierAccountModel = $objTNTOrderModel->getTNTAccountModel();
        if ($objTNTCarrierAccountModel === null) {
            echo TNTOfficiel_Tools::encJSON(
                array(
                    'error' => sprintf(
                        TNTOfficiel::getCodeTranslate('errorUnableLoadForIDStr'),
                        'TNT Account',
                        'TNTOfficielOrder',
                        $intOrderID
                    ),
                )
            );

            return false;
        }

        // Validate.
        $arrResult = $this->processAjaxUpdateOrderParcels($objTNTOrderModel);

        $strPickUpNumber = $objTNTCarrierAccountModel->pickup_display_number ? $objTNTOrderModel->pickup_number : null;

        $this->context->smarty->assign(
            array(
                'strPickUpNumber' => $strPickUpNumber,
                'objTNTOrderModel' => $objTNTOrderModel,
            )
        );

        $arrResult['template'] = $this->context->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/displayAjaxUpdateOrderParcels.tpl'
        );

        echo TNTOfficiel_Tools::encJSON($arrResult);

        return true;
    }

    /**
     * Checks the shipping.
     *
     * @return bool
     */
    public function displayAjaxCheckShippingDateValid()
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)Tools::getValue('orderId');
        // Y-m-d format. Filter and validation done by updatePickupDate().
        $strShippingDate = trim(pSQL(Tools::getValue('shippingDate')));

        $arrResultPickupDate = array(
            'boolIsRequestComError' => false,
            'strResponseMsgError' => null,
            'strResponseMsgWarning' => null,
            'shippingDate' => null,
            'dueDate' => null,
        );

        // Load TNT order info for its ID.
        $objTNTOrderModel = TNTOfficielOrder::loadOrderID($intOrderID, false);
        // If fail.
        if ($objTNTOrderModel === null) {
            $arrResultPickupDate['strResponseMsgError'] = sprintf(
                TNTOfficiel::getCodeTranslate('errorUnableLoadIDStr'),
                'TNTOfficielOrder',
                $intOrderID
            );

            echo TNTOfficiel_Tools::encJSON($arrResultPickupDate);

            return false;
        }

        // Try to update the requested shipping date.
        $arrResultPickupDate = array_merge(
            $arrResultPickupDate,
            $objTNTOrderModel->updatePickupDate($strShippingDate)
        );

        $arrResultPickupDate['shippingDate'] = $objTNTOrderModel->shipping_date;
        $arrResultPickupDate['dueDate'] = $objTNTOrderModel->due_date;

        // Format due date.
        if (is_string($arrResultPickupDate['shippingDate'])) {
            // Timestamp.
            $arrResultPickupDate['shippingDate'] =
                TNTOfficiel_Tools::getDateTimeFormat($arrResultPickupDate['shippingDate']);
        }
        // Format due date.
        if (is_string($arrResultPickupDate['dueDate'])) {
            // Timestamp.
            $arrResultPickupDate['dueDate'] = TNTOfficiel_Tools::getDateTimeFormat($arrResultPickupDate['dueDate']);
        }

        echo TNTOfficiel_Tools::encJSON($arrResultPickupDate);

        return true;
    }
}
