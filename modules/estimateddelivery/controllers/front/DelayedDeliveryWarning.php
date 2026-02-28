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
 * @version 3.5.4
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.5.4                      *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class EstimatedDeliveryDelayedDeliveryWarningModuleFrontController extends ModuleFrontController
{
    private $test_mode = false;
    private $test_type;
    private $test_orders_list;
    private $stats = ['total' => 0, 'admin' => 0, 'client' => 0];

    public function display()
    {
        SmartForm::init($this->module);
        if (Tools::getValue('cron_secret_key') != Configuration::get('ed_cron_secret_key')) {
            exit("You can't access this page");
        }
        if (Configuration::get('ed_enable_delayed_delivery')) {
            $this->test_mode = (bool) Configuration::get('ed_dd_test_mode');
            $schedule_time_for_admin = Configuration::get('ed_dd_admin_hours');
            $schedule_time_for_customer = Configuration::get('ed_dd_customer_hours');
            if ($this->test_mode) {
                $this->test_type = Configuration::get('ed_dd_test_orders_mode');
                if ($this->test_type == 'file') {
                    $path = _PS_MODULE_DIR_ . $this->module->name . '/logs';
                    $fp = fopen($path . '/dd_test_results.txt', 'w+');
                    if ($fp === false) {
                        echo $this->l('The module could not create the file to store the results of the test. You will have to create it manually and run it again.') . SmartForm::genDesc('', '', 'br') . "\n";
                        echo sprintf($this->module->l('The location to create the file should be %s'), SmartForm::genDesc($path . '/dd_test_results.txt', 'strong')) . SmartForm::genDesc('', '', 'br') . "\n";

                        return false;
                    } else {
                        $output = $this->l('Delayed Delivery Warnings test run') . "\n";
                        $output .= $this->l('The test did run at:') . ' ' . date($this->context->language->date_format_full) . " \n";
                        $output .= sprintf($this->l('Processing orders up to %s days'), (int) Configuration::get('ed_dd_days_limit')) . "\n";
                        $output .= "-------------------- \n\n";
                        $output .= $this->l('Admin Data') . ":\n";
                        $output .= $this->l('Hours to notify the admin') . ': ' . $schedule_time_for_admin . "\n";
                        $output .= sprintf($this->l('Date limit for Orders: %s'), date($this->context->language->date_format_full, strtotime('-' . $schedule_time_for_admin . ' days'))) . " \n";
                        $output .= "-------------------- \n\n";
                        $output .= $this->l('Customer Data') . ":\n";
                        $output .= $this->l('Hours to notify the customer') . ': ' . $schedule_time_for_customer . "\n";
                        $output .= sprintf($this->l('Date limit for Orders: %s'), date($this->context->language->date_format_full, strtotime('-' . $schedule_time_for_customer . ' days'))) . " \n";
                        $output .= "-------------------- \n\n\n\n";
                        if (fwrite($fp, $output) === false) {
                            echo $this->module->l('Error while saving the headers') . "\n";

                            return false;
                        }
                    }
                } else {
                    if (!Validate::isEmail(Configuration::get('ed_dd_test_orders_email'))) {
                        echo sprintf($this->l('The email configured for Delayed Delivery Test %s is not correct, please review on section 5.1 and fix it before running the test'), '"' . Configuration::get('ed_dd_test_orders_email') . '"');

                        return false;
                    }
                }
                // Get the orders list to test
                $this->getTestOrdersList();
            }

            if ($this->test_mode && $this->test_type == 'email') {
                $dd_admin_email = Configuration::get('ed_dd_test_orders_email');
                $enable_cc_email = false;
            } else {
                $dd_admin_email = Configuration::get('ed_dd_admin_email') ?: Configuration::get('PS_SHOP_EMAIL');
                $enable_cc_email = Configuration::get('ed_enable_cc_email');
            }

            // get the unshipped orders
            $unshipped_order_list = $this->getUnshippedOrders();

            $psImageUrl = _PS_IMG_;
            $data = [
                '{shop_logo}' => Configuration::hasKey('PS_LOGO') ? $this->context->shop->getBaseURL(true, true) . $psImageUrl . Configuration::get('PS_LOGO') : '',
                '{shop_url}' => Context::getContext()->shop->getBaseURL(true),
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            ];

            // $temp = array();
            if (isset($fp)) {
                fwrite($fp, sprintf($this->module->l('%s orders found to check'), count($unshipped_order_list)));
            }
            if ($unshipped_order_list !== false && count($unshipped_order_list) > 0) {
                foreach ($unshipped_order_list as $order) {
                    if (isset($fp)) {
                        fwrite($fp, "\n\n" . sprintf($this->module->l('Processing order %s'), $order['id_order'] . ' (' . $order['reference'] . ')'));
                    }
                    if ($this->test_mode && (!empty($this->test_orders_list)) && !$this->isOrderInTestList($order)) {
                        if (isset($fp)) {
                            fwrite($fp, ' ' . $this->module->l('Skipped, not in test list'));
                            --$this->stats['orders'];
                        }
                        continue;
                    }
                    if ($this->test_mode && $this->test_type == 'email') {
                        $order['customer_email'] = Configuration::get('ed_dd_test_orders_email');
                    }
                    $current_time = date('Y-m-d H:00:00');
                    $delivery_min = date('Y-m-d H:00:00', strtotime($order['delivery_max'])); // UPDATED from delivery_min to delivery_max
                    $time_to_notify_admin = date('Y-m-d H:00:00', strtotime($delivery_min . ' -' . $schedule_time_for_admin . ' hours'));
                    $time_to_notify_customer = date('Y-m-d H:00:00', strtotime($delivery_min . ' -' . $schedule_time_for_customer . ' hours'));

                    $orderObj = new Order($order['id_order']);
                    $data['{id_order}'] = (int) $order['id_order'];
                    $data['{order_id}'] = (int) $order['id_order'];
                    $data['{order_name}'] = $orderObj->getUniqReference();
                    $data['{order_Reference}'] = $order['reference'];
                    $data['{shipping_number}'] = $orderObj->getWsShippingNumber();
                    // $topic = $order['state_name'];
                    if (isset($fp)) {
                        fwrite($fp, "\n" . sprintf($this->module->l('Minimum delivery date %s'), $delivery_min));
                    }
                    if ((int) $schedule_time_for_admin > 0 && ($current_time >= $time_to_notify_admin)) {
                        if (isset($fp)) {
                            fwrite($fp, "\n\n" . sprintf($this->module->l('%s Delayed delivery warning will be sent.'), $this->l('Admin')));
                            fwrite($fp, "\n" . sprintf($this->module->l('Date limit %s already surpassed') . "\n\n", $time_to_notify_admin));
                        }
                        // send an email the admin for the delayed order and setting the admin_notified flag if not set
                        if ((int) $order['is_admin_notified'] != 1) {
                            ++$this->stats['admin'];
                            if ($this->test_mode && $this->test_type == 'file') {
                                $this->writeTestData($fp, $orderObj, $data, $dd_admin_email);
                            } else {
                                $this->sendEmailNotification($orderObj, $data, 'delayed_shipment_admin', $dd_admin_email, $order);
                            }
                        }
                    } else {
                        if (isset($fp)) {
                            fwrite($fp, "\n" . $this->module->l('Admin not notified. Order is still within time limit'), $delivery_min);
                        }
                    }
                    if ((int) $schedule_time_for_customer > 0 && ($current_time >= $time_to_notify_customer) && $order['customer_email'] != '') {
                        if (isset($fp)) {
                            fwrite($fp, "\n\n" . sprintf($this->module->l('%s Delayed delivery warning will be sent.'), $this->l('Customer')));
                            fwrite($fp, "\n" . sprintf($this->module->l('Date limit %s already surpassed') . "\n\n", $time_to_notify_admin));
                        }
                        $data['{firstname}'] = $order['customer_firstname'];
                        $data['{lastname}'] = $order['customer_lastname'];
                        // send an email the customer for the delayed order and setting the client_notified flag if not set
                        if ((int) $order['is_client_notified'] != 1) {
                            ++$this->stats['client'];
                            if ($this->test_mode && $this->test_type == 'file') {
                                $this->writeTestData($fp, $orderObj, $data, $order['customer_email'], $dd_admin_email);
                            } else {
                                $this->sendEmailNotification($orderObj, $data, 'delayed_shipment', $dd_admin_email, $order, $enable_cc_email);
                            }
                        }
                    }
                }
            }
            if ($this->test_mode) {
                echo SmartForm::genDesc($this->l('Test executed successfully'), 'h2') . "\n";
                echo SmartForm::genDesc($this->l('Total Orders') . ': ' . $this->stats['orders'], 'h3') . "\n";
                echo SmartForm::genDesc($this->l('Messages sent to admin: ') . ': ' . $this->stats['admin'], 'h3') . "\n";
                echo SmartForm::genDesc($this->l('Messages sent to customers: ') . ': ' . $this->stats['client'], 'h3') . "\n";
                if ($this->test_type == 'file') {
                    $test_path = dirname(__FILE__) . '/../../logs/dd_test_results.txt';
                    echo SmartForm::openTag('p') . SmartForm::openTag('a', 'href="' . $this->context->link->getBaseLink() . 'modules/' . $this->module->name . '/logs/dd_test_results.txt" target="_blank"') . SmartForm::genDesc($this->l('Open Last Test results (%s)'), '', '', [date($this->context->language->date_format_full, filemtime($test_path))]) . SmartForm::closeTag('a') . SmartForm::closeTag('p') . "\n";
                } else {
                    echo SmartForm::genDesc($this->l('Delayed Delivery messages have been sent to %s'), 'p', null, [$dd_admin_email]);
                }
            } else {
                echo SmartForm::genDesc($this->l('Cron executed successfully'), 'h2') . "\n";
                echo SmartForm::genDesc($this->l('Total Orders') . ': ' . $this->stats['orders'], 'h3') . "\n";
                echo SmartForm::genDesc($this->l('Messages sent to admin: ') . ': ' . $this->stats['admin'], 'h3') . "\n";
                echo SmartForm::genDesc($this->l('Messages sent to customers: ') . ': ' . $this->stats['client'], 'h3') . "\n";
            }
        }
    }

    private function getUnshippedOrders()
    {
        $order_status_to_check = (int) Configuration::get('ed_dd_order_state');
        $date_limit = (int) Configuration::get('ed_dd_days_limit');
        if ($date_limit > 0) {
            $date_limit = date('Y-m-d H:i:s', strtotime('-' . $date_limit . ' days'));
        }
        $sql = 'SELECT o.*, (
                SELECT osl.`name`
                FROM `' . _DB_PREFIX_ . 'order_state_lang` osl
                WHERE osl.`id_order_state` = o.`current_state`
                AND osl.`id_lang` = ' . (int) $this->context->language->id . '
            ) AS `state_name`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`, 
            eo.delivery_min AS delivery_min, eo.delivery_max AS delivery_max, eo.admin_notified AS `is_admin_notified`, eo.client_notified AS `is_client_notified`,
            c.firstname AS customer_firstname, c.lastname AS customer_lastname, c.email AS customer_email
            FROM `' . _DB_PREFIX_ . 'orders` o
            LEFT JOIN `' . _DB_PREFIX_ . 'ed_orders` eo ON (eo.id_order = o.id_order)
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = o.id_customer)
            WHERE eo.delivery_min IS NOT NULL 
                AND eo.delivery_max IS NOT NULL 
                AND eo.shipped = 0 
                AND o.valid = 1 
                AND o.current_state <> ' . (int) Configuration::get('PS_OS_CANCELED') .
                ($date_limit ? ' AND o.date_add > \'' . $date_limit . '\'' : '') . ' 
                AND o.id_order NOT IN (SELECT id_order FROM `' . _DB_PREFIX_ . 'order_history` WHERE id_order_state = ' . $order_status_to_check . ')
            ORDER BY o.date_add DESC';

        $ret = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($ret === false) {
            echo $this->module->l('Error') . ': ' . Db::getInstance()->getMsgError();
        } elseif (count($ret) == 0) {
            echo SmartForm::genDesc($this->l('Excellent! you don\'t have any order with a delayed delivery'), 'h2');
            exit;
        } else {
            $this->stats['orders'] = count($ret);

            return $ret;
        }
    }

    private function isOrderInTestList($order)
    {
        if ($this->test_orders_list != false) {
            foreach ($this->test_orders_list as $idOrRef) {
                if ($order['id_order'] == $idOrRef || $order['reference'] == $idOrRef) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getTestOrdersList()
    {
        $orders = str_replace(' ', '', Configuration::get('ed_dd_test_orders'));
        if (trim($orders) == '') {
            $this->test_orders_list = false;
        } else {
            $order = explode(',', $orders);
            if (!empty($orders) && count($orders) > 0) {
                $this->test_orders_list = $order;
            } else {
                exit($this->module->l('Aborting test mode, the order IDs / Reference list is not valid'));
            }
        }
    }

    private function sendEmailNotification($orderObj, $data, $template, $dd_admin_email, $order = [], $enable_cc_email = false)
    {
        if ($template == 'delayed_shipment') {
            $column = 'client_notified';
        } else {
            $column = 'admin_notified';
        }
        $sent = Mail::Send(
            (int) $orderObj->id_lang,
            $template,
            sprintf(Mail::l('Delayed delivery on order # %s', $orderObj->id_lang), $order['reference']),
            $data,
            isset($order['customer_email']) ? $order['customer_email'] : false,
            isset($order['customer_email']) ? $order['customer_firstname'] . ' ' . $order['customer_lastname'] : false,
            Configuration::get('PS_SHOP_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            _PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/mails/',
            false,
            (int) $orderObj->id_shop,
            $enable_cc_email ? $dd_admin_email : null
        );
        if ($sent !== false) {
            echo "\n" . SmartForm::genDesc($this->module->l('Order %s (%s): %s reminder sent'), 'p', null, [$order['id_order'], $order['reference'], $template == 'delayed_shipment' ? $this->module->l('Customer') : $this->module->l('Admin')]);
            if (!$this->test_mode) {
                // Update the database only if not in test mode
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ed_orders` SET ' . bqSQL($column) . ' = 1 WHERE id_order = ' . (int) $orderObj->id);
            }
        } else {
            echo "\n" . SmartForm::genDesc($this->module->l('Error while tring to send the email for the order %s (%s) to the %s'), 'p', null, [$order['id_order'], $order['reference'], $template == 'delayed_shipment' ? $this->module->l('Customer') : $this->module->l('Admin')]);
        }
    }

    private function writeTestData($fp, $orderObj, $data, $email, $email_cc = false)
    {
        $output = $this->module->l('Order') . ':' . $orderObj->id . ' (' . $orderObj->reference . ')' . "\n";
        $output .= $this->module->l('To') . ':' . $email . "\n";
        $output .= $this->module->l('CC') . ':' . $email_cc . "\n";
        $output .= $this->module->l('Title') . ':' . sprintf(Mail::l('Delayed delivery on order # %s', $orderObj->id_lang), $orderObj->reference) . "\n";
        $output .= $this->module->l('Variables') . ':' . "\n";
        $output .= print_r($data, true) . "\n";
        $output .= "-------------------- \n\n";
        $fp = fwrite($fp, $output);
        if ($fp === false) {
            echo 'An error occurred when trying to save the test data';
        }
    }

    public function displayAjaxReviewPastOrders()
    {
        $order_status_to_check = Configuration::get('ed_dd_order_state');
        // $sql = 'SELECT id_order FROM `'._DB_PREFIX_.'ed_orders` WHERE shipped = 0';
        // $unshipped_order_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $shipped_order_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_order
            FROM `' . _DB_PREFIX_ . 'orders` o LEFT JOIN `' . _DB_PREFIX_ . 'ed_orders` eo USING (id_order)
                WHERE eo.delivery_min IS NOT NULL AND eo.delivery_max IS NOT NULL AND id_order IN (SELECT id_order FROM `' . _DB_PREFIX_ . 'order_history` WHERE id_order_state = ' . (int) $order_status_to_check . ')');
        if ($shipped_order_list !== false && count($shipped_order_list) > 0) {
            Db::getInstance()->update('ed_orders', 'shipped = 1', 'id_order IN (' . implode(',', $shipped_order_list));
        }
        echo json_encode('success');
    }
}
