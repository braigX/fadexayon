<?php
/**
 ** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPickingListController extends ModuleAdminController
{
    // public $content = '';
    // private $postErrors = array();
    private static $prefix = 'ed_';
    private $initial_columns = [];
    protected $position_identifier = 'id_order';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'orders';
        $this->className = 'Order';
        $this->list_id = 'order';
        $this->identifier = 'id_order';
        $this->display = 'list';
        $this->allow_export = false;
        $this->context = Context::getContext();
        $this->id_lang = $this->context->language->id;

        $inOrderStateID = '';
        $excluded_states = json_decode(Configuration::get(self::$prefix . 'picking_order_state'), true);
        if (!empty($excluded_states) && count($excluded_states) > 0) {
            foreach ($excluded_states as $excluded_state) {
                $inOrderStateID .= (int) $excluded_state . ',';
            }
            $inSQL = ' AND a.`current_state` NOT IN (' . rtrim($inOrderStateID, ',') . ') ';
        } else {
            $inSQL = '';
        }

        $this->_select = 'CONCAT(cs.`firstname`," ",cs.`lastname`) AS firstname, cs.`email` AS email, addr.phone as phone, edo.picking_day as picking_day, carr.name as delivery_method';

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` AS cs ON (a.`id_customer` = cs.`id_customer`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'address` AS addr ON (cs.`id_customer` = addr.`id_customer`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'ed_orders` AS edo ON (a.`id_order` = edo.`id_order`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'carrier` AS carr ON (edo.`id_carrier` = carr.`id_carrier`)';

        $this->_where = ' AND a.`id_order` = edo.`id_order` ' . $inSQL . ' AND a.valid = 1';

        $this->_orderWay = 'DESC';

        $this->_group .= 'GROUP by a.id_order';

        // Add Amazon Marketplace Module data if it's available
        if (Module::isEnabled('amazon')) {
            $df = $this->getMySQLDateFormat();
            // Add Amazon related data
            // $this->_select .= ', COALESCE(edo.picking_day, CONCAT(DATE_FORMAT(amz.`earliest_ship_date`, "'.$df.'"), " - ", DATE_FORMAT(amz.`latest_ship_date`, "'.$df.'"))) AS picking_day';
            $this->_select .= ', CONCAT(COALESCE(edo.delivery_min, DATE_FORMAT(amz.`latest_ship_date`, "' . $df . '")), "_", edo.undefined_delivery) AS delivery_min, CONCAT(COALESCE(edo.picking_day, DATE_FORMAT(amz.`latest_ship_date`, "' . $df . '")), "_", edo.undefined_delivery) AS picking_day';
            $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'marketplace_orders` AS amz ON (amz.`id_order` = a.`id_order`)';
        } else {
            $this->_select .= ', CONCAT(edo.delivery_min, "_", edo.undefined_delivery, "_", edo.id_order) AS delivery_min, CONCAT(edo.picking_day, "_", edo.undefined_delivery, "_", edo.id_order) as picking_day';
        }
        parent::__construct();
        $this->fields_list = [
            'picking_day' => [
                'title' => $this->l('Picking Limit'),
                'hint' => $this->l('The date limit in which the order should be prepared and ready to be shipped'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'formatPickingDateForOrderList',
                'order_key' => 'picking_day',
                'search' => false,
                'orderby' => true,
            ],
            'delivery_min' => [
                'title' => $this->l('Min. Delivery Date'),
                'hint' => $this->l('The estimated date in which the customer should be able to receive the order'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'formatDeliveryDateForOrderList',
                'order_key' => 'delivery_min',
                'search' => false,
                'orderby' => true,
            ],
            'id_order' => [
                'title' => $this->l('ID'),
                'hint' => $this->l('The ID of the order'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!id_order',
                'order_key' => 'a!id_order',
            ],
            'reference' => [
                'title' => $this->l('Order Reference'),
                'hint' => $this->l('The reference of the order'),
                'filter_key' => 'a!reference',
                'align' => 'center',
                'class' => 'fixed-width-xm',
                'orderby' => false,
            ],
            'firstname' => [
                'title' => $this->l('Client Name'),
                'hint' => $this->l('The name of the client'),
                'orderby' => false,
                'search' => true,
                'align' => 'left',
                'class' => 'fixed-width-xs',
                'filter_key' => 'cs!firstname',
                'width' => 'auto',
            ],
            'email' => [
                'title' => $this->l('Email'),
                'hint' => $this->l('The contact email of the client'),
                'filter_key' => 'cs!email',
                'align' => 'center',
                'class' => 'fixed-width-xm',
                'orderby' => false,
            ],
            'phone' => [
                'title' => $this->l('Delivery Phone'),
                'hint' => $this->l('The phone registered in the delivery address'),
                'filter_key' => 'addr!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ],
            'payment' => [
                'title' => $this->l('Payment Method'),
                'hint' => $this->l('The payment method used when the order was placed'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xm',
                'orderby' => false,
            ],
            'delivery_method' => [
                'title' => $this->l('Delivery Method'),
                'hint' => $this->l('The delivery method selected when the order was placed'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ],
        ];
        $this->saveInitialColumns();
        $this->filterUnselectedFields();
        $this->addRowAction('view');

        /*$this->bulk_actions = [
            'ship' => [
                'text' => $this->trans('Ship selected', [], 'Admin.Actions'),
                'icon' => 'icon-truck',
                'confirm' => $this->trans('Mark ship selected items?', [], 'Admin.Notifications.Warning'),
            ],
        ];*/
    }

    private function saveInitialColumns()
    {
        $this->initial_columns = $this->fields_list;
    }

    /**
     * Filter the fields unselected in the controller options page
     */
    private function filterUnselectedFields()
    {
        $selected_columns = json_decode(Configuration::get(self::$prefix . 'picking_selected_columns'), true);
        if (!empty($selected_columns)) {
            foreach ($this->fields_list as $key => $value) {
                if (!isset($selected_columns[$key])) {
                    unset($this->fields_list[$key]);
                }
            }
        }
    }

    /**
     * Initialize Content
     */
    public function initContent()
    {
        parent::initContent();

        // TODO review order ids to be excluded
        $orderStateToBeExcluded = [1, 10, 13, 14, 15, 16, 17, 19];
        $orderStates = OrderState::getOrderStates($this->id_lang);
        $excluded_states = json_decode(Configuration::get(self::$prefix . 'picking_order_state'), true);
        $selected_columns = json_decode(Configuration::get(self::$prefix . 'picking_selected_columns'), true);

        foreach ($orderStates as $orderState) {
            if (!in_array($orderState['id_order_state'], $orderStateToBeExcluded)) {
                $newOrderStates[] = $orderState;
            }
        }

        $this->context->smarty->assign(
            [
                'order_states' => $newOrderStates,
                'available_columns' => $this->getColumnNames(),
                'token' => $this->token,
                'excluded_states' => $excluded_states,
                'selected_columns' => $selected_columns,
            ]
        );
        $this->content .= $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/controller/admin-picking-options.tpl');

        if ($this->display == 'view') {
            $parameters = ['vieworder' => 1, 'id_order' => (int) Tools::getValue('id_order')];
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders', true, [], $parameters));
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    private function getColumnNames()
    {
        $ret = [];
        foreach ($this->initial_columns as $column_name => $values) {
            $ret[] = ['id' => $column_name, 'name' => $values['title']];
        }

        return $ret;
    }

    /**
     * Initialize Header Toolbar
     */
    public function initToolbar()
    {
        parent::initToolbar();
    }

    public function setMedia($isNewTheme = false)
    {
        $this->_path = '/modules/estimateddelivery/';
        $this->admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
        $this->admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $this->admin_webpath);

        parent::setMedia($isNewTheme);

        $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/default/js/vendor/bootstrap/tooltip.js');
        $this->context->controller->addJS($this->_path . 'views/js/order_picking_list.js');
    }

    /**
     * Create the form that will be displayed in the controller page of your module.
     */
    public function renderForm()
    {
        return parent::renderForm();
    }

    private function postValidation()
    {
    }

    /**
     * Check form value Int of Not
     */
    public function isInt($value)
    {
        return (string) (int) $value === (string) $value or $value === false;
    }

    /**
     * Display form Errors
     */
    public function displayError($err)
    {
        $this->errors[] = $err;
    }

    /**
     * Process form Data
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkshiporders') == true) {
            $order_ids = Tools::getValue('orderBox');

            if (count($order_ids) == 0) {
                return false;
            }

            $orderStates = OrderState::getOrderStates($this->id_lang);

            foreach ($orderStates as $orderState) {
                if ($orderState['name'] == 'Shipped') {
                    $orderShippingState = $orderState['id_order_state'];
                }
            }

            foreach ($order_ids as $order_id) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'ed_orders`
                    SET `shipped` = 1
                    WHERE `id_order` = ' . (int) $order_id;
                Db::getInstance()->execute($sql);

                $orderHistory = new OrderHistory();
                $orderHistory->changeIdOrderState($orderShippingState, $order_id);
            }

            return;
        }

        if (Tools::isSubmit('submitOrderState')) {
            $ids = Tools::getValue('order_state');
            $this->processSelectedFields($ids, 'picking_order_state');

            return;
        }
        if (Tools::isSubmit('submitSelectedColumns')) {
            $ids = Tools::getValue('selected_columns');
            $this->processSelectedFields($ids, 'picking_selected_columns');

            return;
        }
        parent::postProcess();
    }

    private function processSelectedFields($ids_list, $key)
    {
        Configuration::updateValue(self::$prefix . $key, json_encode($ids_list));
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminPickingList', true, [], []));
    }

    /**
     * Formats the date to highlight the days until the picking
     *
     * @param $date
     */
    public function formatPickingDateForOrderList($date)
    {
        return $this->formatDateForOrderList($date, $this->module, 'picking');
    }

    /**
     * Formats the date to highlight the days until the minimum delivery
     *
     * @param $date
     */
    public function formatDeliveryDateForOrderList($date)
    {
        return $this->formatDateForOrderList($date, $this->module, 'delivery');
    }

    private function formatDateForOrderList($date, $module, $palete)
    {
        $date = explode('_', $date);
        if (isset($date[1]) && $date[1]) {
            $date[0] = $this->l('Undefined');
        }

        return EDTools::formatDateForOrderList($date[0], $module, $palete, isset($date[2]) ? $date[2] : 0);
    }

    private function getMySQLDateFormat()
    {
        $separators = ['/', '-', '|'];
        foreach ($separators as $sep) {
            if (Tools::strpos($this->context->language->date_format_lite, $sep) !== false) {
                $df = explode($sep, $this->context->language->date_format_lite);
                foreach ($df as &$f) {
                    $f = '%' . $f;
                }

                return implode($sep, $df);
            }
        }
    }
}
