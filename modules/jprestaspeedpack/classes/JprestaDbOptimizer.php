<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaSubModule;
use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaDbOptimizer')) {
    require_once 'JprestaSubModule.php';

    class JprestaDbOptimizer extends JprestaSubModule
    {
        public function __construct($module)
        {
            parent::__construct($module);
        }

        public function saveConfiguration()
        {
            $output = '';
            if (Tools::isSubmit('submitDbOptimizer') || Tools::isSubmit('submitDbOptimizerClean')) {
                if (_PS_MODE_DEMO_ && !Context::getContext()->employee->isSuperAdmin()) {
                    $output .= $this->module->displayError($this->module->l('In DEMO mode you cannot modify the configuration.', 'jprestadboptimizer'));
                } else {
                    //
                    // Enable / disable dbOptimizer
                    //
                    $newEnable = (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_ENABLE');
                    $oldEnable = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_ENABLE');
                    if ($newEnable && !$oldEnable) {
                        Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_ENABLE', 1);
                    } elseif (!$newEnable && $oldEnable) {
                        Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_ENABLE', 0);
                    }

                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS',
                        max(0,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS', 30)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS',
                        max(1,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS', 90)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS',
                        max(1,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS', 90)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS',
                        max(1,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS', 30)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS',
                        max(1,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS', 30)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_404_DAYS',
                        max(0,
                            min(3000,
                                (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_404_DAYS', 30)
                            )
                        )
                    );
                    Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_OPTIMIZE',
                        (int) Tools::getValue('SPEED_PACK_DBOPTIMIZER_OPTIMIZE')
                    );

                    $output .= $this->module->displayConfirmation($this->module->l('Settings updated', 'jprestadboptimizer'));
                }
            }
            if (Tools::isSubmit('submitDbOptimizerClean')) {
                $output .= $this->module->displayConfirmation(nl2br($this->clean('manual')));
            }

            return $output;
        }

        public function displayForm()
        {
            $smarty = Context::getContext()->smarty;
            $smarty->assign('urls', $this->getCronURLs());
            $urls = $smarty->fetch(_PS_MODULE_DIR_ . '/jprestaspeedpack/views/templates/admin/dboptimizer_urls.tpl');

            $isEnabled = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_ENABLE');
            // Init Fields form array
            $fieldsForm = [];
            $fieldsForm[0]['form'] = [
                'legend' => [
                    'title' => $this->module->l('Database cleaner and optimizer', 'jprestadboptimizer'),
                ],
                'input' => [
                    [
                        'type' => 'alert_info',
                        'name' => 'SPEED_PACK_DBOPTIMIZER_INFO',
                        'text' => $this->l('To clean and optimize your database periodically (once a day is enought) your can use the following URL (one for each shop) in a CRON job:', 'jprestadboptimizer')
                            . $urls
                            . $this->l('Some subscription plans for the JPresta-Cache-Warmer service include automatic execution of this cleaning process. Just ensure that it is enabled in your cache-warmer settings.', 'jprestadboptimizer'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Enable', 'Admin.Actions'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_ENABLE',
                        'is_bool' => true,
                        'desc' => $this->module->l('Enable database cleaner and optimizer', 'jprestadboptimizer'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', 'Admin.Global'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Connections logs of visitors (IP, date, pages, origin/referer)', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 0,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep these logs', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Abandonned carts and their guest visitor', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 1,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep guests and abandonned carts (minimum 1 but we recommend at least 7)', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Abandonned carts of customers', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 7,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep abandoned carts of registered customers (minimum 7 but we recommend at least 30)', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Prestashop logs with severity 1 and 2', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 1,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep logs with severity 1 and 2 (minimum 1 but we recommend at least 7)', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Prestashop logs with severity 3 and 4', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 1,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep logs with severity 3 and 4 (minimum 1 but we recommend at least 7)', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'integer',
                        'label' => $this->module->l('Not found pages logs', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_404_DAYS',
                        'class' => 'maxwidth10rem',
                        'min' => 0,
                        'max' => 3000,
                        'desc' => $this->module->l('Number of days you want to keep not found pages logs', 'jprestadboptimizer'),
                        'suffix' => $this->module->l('days', 'jprestadboptimizer'),
                        'disabled' => !$isEnabled,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->module->l('Optimize tables', 'jprestadboptimizer'),
                        'name' => 'SPEED_PACK_DBOPTIMIZER_OPTIMIZE',
                        'is_bool' => true,
                        'desc' => $this->module->l('Optimize database if need', 'jprestadboptimizer'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->module->l('Enabled', 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->module->l('Disabled', 'Admin.Global'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save', 'Admin.Actions'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitDbOptimizer',
                ],
            ];

            if ($isEnabled) {
                $fieldsForm[0]['form']['buttons'] = [
                    [
                        'title' => $this->module->l('Clean now!', 'jprestadboptimizer'),
                        'type' => 'submit',
                        'icon' => 'process-icon-eraser',
                        'class' => 'btn-primary pull-right',
                        'name' => 'submitDbOptimizerClean',
                    ],
                ];
            }

            $helper = new HelperForm();
            $helper->module = $this->module;

            // Load current value
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_ENABLE'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_ENABLE');
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS', null, null, null, 30);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS', null, null, null, 90);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS', null, null, null, 90);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS', null, null, null, 15);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS', null, null, null, 45);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_404_DAYS'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_404_DAYS', null, null, null, 30);
            $helper->fields_value['SPEED_PACK_DBOPTIMIZER_OPTIMIZE'] = (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_OPTIMIZE', null, null, null, true);

            return $helper->generateForm($fieldsForm);
        }

        public function install()
        {
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS', 30);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS', 90);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS', 90);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS', 15);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS', 45);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_404_DAYS', 30);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_OPTIMIZE', 1);
            Configuration::updateValue('SPEED_PACK_DBOPTIMIZER_ENABLE', 1);

            return true;
        }

        public function clean($reason = 'unknown')
        {
            $startTime = microtime(true);
            $output = 'Jprestaspeedpack - dboptimizer - ' . date('Y-m-d H:i:s') . "\n";
            if ((int) Configuration::get('SPEED_PACK_DBOPTIMIZER_ENABLE')) {
                $output .= $this->cleanConnect();
                $output .= $this->cleanGuestCarts();
                $output .= $this->cleanCustomerCarts();
                $output .= $this->cleanLogs12();
                $output .= $this->cleanLogs34();
                $output .= $this->clean404();
                $output .= $this->optimize();
                JprestaUtils::addLog("DbOptimize | clean($reason) done in " . number_format(microtime(true) - $startTime, 3) . ' second(s)', 1, null, null, null, true);
            } else {
                $output = $this->module->l('Nothing was cleaned: database optimisation is disabled', 'jprestadboptimizer');
                JprestaUtils::addLog("DbOptimize | clean($reason) is disabled", 1, null, null, null, true);
            }

            return $output;
        }

        public function cleanConnect($timeoutSeconds = 120)
        {
            $blockSize = 10000;
            $output = '';
            try {
                $startTime = mktime(true);
                $dateBefore = strtotime('-' . (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_CONNECT_DAYS') . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);
                $id_shop = Shop::getContextShopID(true);
                $id_shop_group = Shop::getContextShopGroupID(true);

                $output .= '  - deleting connections previous to ' . $dateBeforeSql . ': ';

                $loopCount = 0;
                $totalDeletedRows = 0;
                $deleteMore = true;
                $spentSeconds = 0;
                while ($deleteMore) {
                    $query = 'DELETE c.*, p.*, s.*
                    FROM `' . _DB_PREFIX_ . 'connections` c
                    LEFT JOIN `' . _DB_PREFIX_ . 'connections_page` p ON p.id_connections = c.id_connections
                    LEFT JOIN `' . _DB_PREFIX_ . 'connections_source` s ON s.id_connections = c.id_connections
                    JOIN (
                        SELECT c2.id_connections
                        FROM `' . _DB_PREFIX_ . 'connections` c2
                        WHERE c2.date_add < \'' . pSQL($dateBeforeSql) . '\'' .
                        ($id_shop !== null ? ' AND c2.id_shop=' . (int) $id_shop : '') .
                        ($id_shop_group !== null ? ' AND c2.id_shop_group=' . (int) $id_shop_group : '') .
                        ' LIMIT ' . (int) $blockSize . '
                    ) c3 ON c.id_connections = c3.id_connections;';

                    JprestaUtils::dbExecuteSQL($query, true, true);

                    $deletedRows = Db::getInstance()->Affected_Rows();
                    $totalDeletedRows += $deletedRows;
                    $spentSeconds = mktime(true) - $startTime;
                    $deleteMore = $deletedRows > 0 && ($spentSeconds < $timeoutSeconds);
                    ++$loopCount;
                }
                $output .= $totalDeletedRows . " record(s) deleted in $loopCount loop(s) and $spentSeconds seconds\n";
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during cleanConnect: ' . $e->getMessage(), 2, null,
                    null, null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        public function cleanGuestCarts()
        {
            $output = '';
            try {
                $dateBefore = strtotime('-' . max(1, (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_GUESTSCARTS_DAYS')) . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);

                $output .= '  - deleting abandonned carts previous to ' . $dateBeforeSql . ': ';
                $countGuest = 0;
                $countCart = 0;

                $id_shop = Shop::getContextShopID(true);
                $id_shop_group = Shop::getContextShopGroupID(true);
                $queryCarts = 'SELECT * FROM `' . _DB_PREFIX_ . 'cart` WHERE `id_guest` IS NOT NULL AND `id_guest` <> 0
                AND date_upd < \'' . pSQL($dateBeforeSql) . '\'' .
                    ($id_shop !== null ? ' AND id_shop=' . (int) $id_shop : '') .
                    ($id_shop_group !== null ? ' AND id_shop_group=' . (int) $id_shop_group : '') .
                    ' LIMIT 1000' .
                    ';';
                $resultsCarts = JprestaUtils::dbSelectRows($queryCarts, true, true);
                if (JprestaUtils::isIterable($resultsCarts)) {
                    foreach ($resultsCarts as $resultCart) {
                        $idCustomer = (int) $resultCart['id_customer'];
                        // Delete the guest and related informations
                        $this->deleteGuest((int) $resultCart['id_guest']);
                        ++$countGuest;
                        if (!$idCustomer) {
                            // Also delete the cart since it is not attached to any customer
                            $this->deleteCart((int) $resultCart['id_cart']);
                            ++$countCart;
                        }
                    }
                }

                $output .= "$countCart carts and $countGuest guests have been deleted\n";
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during cleanGuestCarts: ' . $e->getMessage(), 2,
                    null, null, null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        public function cleanCustomerCarts()
        {
            $output = '';
            try {
                $dateBefore = strtotime('-' . max(7, (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_CUSTOMERSCARTS_DAYS')) . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);

                $output .= '  - deleting abandoned carts of registered customers previous to ' . $dateBeforeSql . ': ';
                $countCart = 0;

                $id_shop = Shop::getContextShopID(true);
                $id_shop_group = Shop::getContextShopGroupID(true);
                $queryCarts = 'SELECT c.* FROM `' . _DB_PREFIX_ . 'cart` c LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON c.id_cart=o.id_cart WHERE o.id_order IS NULL
                AND c.date_upd < \'' . pSQL($dateBeforeSql) . '\'' .
                    ($id_shop !== null ? ' AND c.id_shop=' . (int) $id_shop : '') .
                    ($id_shop_group !== null ? ' AND c.id_shop_group=' . (int) $id_shop_group : '') .
                    ' LIMIT 1000' .
                    ';';
                $resultsCarts = JprestaUtils::dbSelectRows($queryCarts, true, true);
                if (JprestaUtils::isIterable($resultsCarts)) {
                    foreach ($resultsCarts as $resultCart) {
                        // Delete the cart since it is considered as abandonned
                        $this->deleteCart((int) $resultCart['id_cart']);
                        ++$countCart;
                    }
                }

                $output .= "$countCart carts of registered customers have been deleted\n";
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during cleanCustomerCarts: ' . $e->getMessage(), 2,
                    null, null, null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        protected function deleteCart($idCart)
        {
            $cart = new Cart($idCart);
            if (Validate::isLoadedObject($cart)) {
                $cart->delete();
            }
        }

        protected function deleteGuest($idGuest)
        {
            JprestaUtils::dbExecuteSQL('DELETE g.*, c.*, p.*, s.*
                FROM `' . _DB_PREFIX_ . 'guest` g
                LEFT JOIN `' . _DB_PREFIX_ . 'connections` c ON c.id_guest = g.id_guest
                LEFT JOIN `' . _DB_PREFIX_ . 'connections_page` p ON p.id_connections = c.id_connections
                LEFT JOIN `' . _DB_PREFIX_ . 'connections_source` s ON s.id_connections = c.id_connections
                WHERE g.id_guest=' . (int) $idGuest . ';', true, true);
            JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . 'cart`
                SET id_guest = NULL
                WHERE id_guest=' . (int) $idGuest . ';', true, true);
        }

        public function cleanLogs12()
        {
            $output = '';
            try {
                $dateBefore = strtotime('-' . max(1, (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_LOGS12_DAYS')) . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);

                $output .= '  - deleting logs with severity 1 and 2 previous to ' . $dateBeforeSql . ': ';

                JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . 'log` WHERE severity < 3 AND date_add < \'' . pSQL($dateBeforeSql) . '\';', true, true);

                $output .= Db::getInstance()->Affected_Rows() . " record(s) deleted\n";
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during cleanLogs12: ' . $e->getMessage(), 2, null,
                    null, null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        public function cleanLogs34()
        {
            $output = '';
            try {
                $dateBefore = strtotime('-' . max(1, (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_LOGS34_DAYS')) . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);

                $output .= '  - deleting logs with severity 3 and 4 previous to ' . $dateBeforeSql . ': ';

                JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . 'log` WHERE severity > 2 AND date_add < \'' . pSQL($dateBeforeSql) . '\';', true, true);

                $output .= Db::getInstance()->Affected_Rows() . " record(s) deleted\n";
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during cleanLogs34: ' . $e->getMessage(), 2, null,
                    null, null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        public function clean404()
        {
            $output = '';
            try {
                $dateBefore = strtotime('-' . (int) Configuration::get('SPEED_PACK_DBOPTIMIZER_404_DAYS') . ' days');
                $dateBeforeSql = date('Y-m-d H:i:s', $dateBefore);

                $output .= '  - deleting pages not found previous to ' . $dateBeforeSql . ': ';

                $id_shop = Shop::getContextShopID(true);
                $id_shop_group = Shop::getContextShopGroupID(true);
                $query = 'DELETE FROM `' . _DB_PREFIX_ . 'pagenotfound` WHERE
                date_add < \'' . pSQL($dateBeforeSql) . '\'' .
                    ($id_shop !== null ? ' AND id_shop=' . (int) $id_shop : '') .
                    ($id_shop_group !== null ? ' AND id_shop_group=' . (int) $id_shop_group : '') .
                    ';';
                if (JprestaUtils::dbExecuteSQL($query, false, false)) {
                    $output .= Db::getInstance()->Affected_Rows() . " record(s) deleted\n";
                } else {
                    $output .= "0 record(s) deleted\n";
                }
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during clean404: ' . $e->getMessage(), 2, null, null,
                    null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        /**
         * Optimizes tables that have at least 10% of space to free which represents at least 10Mo
         */
        public function optimize()
        {
            $output = '  - optimizing tables if needed: ';
            try {
                $tables = JprestaUtils::dbSelectRows('SHOW TABLE STATUS
            WHERE Data_free / Data_length > 0.1
            AND Data_free > 1024*1024*10
            AND Name like \'' . pSQL(_DB_PREFIX_ . '%') . '\';', true, true);

                $count = 0;
                if (JprestaUtils::isIterable($tables)) {
                    foreach ($tables as $table) {
                        JprestaUtils::dbExecuteSQL('OPTIMIZE TABLE `' . $table['Name'] . '`', true, true);
                        $output .= $table['Name'] . ', ';
                        ++$count;
                    }
                }

                if ($count === 1) {
                    $output .= "has been optimized\n";
                } elseif ($count > 0) {
                    $output .= "have been optimized\n";
                } else {
                    $output .= "nothing need to be optimized\n";
                }
            } catch (Throwable $e) {
                JprestaUtils::addLog('DbOptimize | Error during optimize: ' . $e->getMessage(), 2, null, null,
                    null, true);
                $output .= "\n    *** Error: " . $e->getMessage() . "\n";
            }

            return $output;
        }

        private function getCronURLs()
        {
            $urls = [];
            foreach (Shop::getContextListShopID() as $shopId) {
                $shop = new Shop($shopId);
                $url = $shop->getBaseURL(true);
                if (JprestaUtils::strlen($url) > 0) {
                    $urls[] = $url . '?fc=module&module=' . $this->module->name . '&controller=dboptimize&token=' . JprestaUtils::getSecurityToken($shopId);
                }
            }

            return $urls;
        }
    }
}
