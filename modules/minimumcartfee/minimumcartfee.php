<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Minimumcartfee extends Module
{
    public function __construct()
    {
        $this->name = 'minimumcartfee';
        $this->tab = 'pricing_promotion';
        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_
        );
        $this->version = '1.0.0';
        $this->author = 'Novatis';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Minimum Cart Fee');
        $this->description = $this->l('Adds a fee if the cart total is below a minimum threshold.');
    }
    
    protected function logToFile(string $msg): void
    {
        $file = __DIR__ . '/uninstall_debug.log';
        $time = date('[Y-m-d H:i:s]');
        @file_put_contents($file, "$time $msg\n", FILE_APPEND);
    }
    
    public function install(): bool
    {
        // Début de l’installation
        $this->logToFile('=== Install started ===');

        // 1) parent::install()
        $this->logToFile('Step 1: calling parent::install()');
        if (!parent::install()) {
            $this->logToFile('❌ parent::install() failed');
            return false;
        }
        $this->logToFile('✅ parent::install() succeeded');

        // 2) installDb()
        $this->logToFile('Step 2: running installDb()');
        if (!$this->installDb()) {
            $this->logToFile('❌ installDb() failed');
            return false;
        }
        $this->logToFile('✅ installDb() succeeded');

        // 3) installOverrides()
        $this->logToFile('Step 3: installing overrides');
        if (!$this->installOverrides()) {
            $this->logToFile('❌ installOverrides() failed');
            return false;
        }
        $this->logToFile('✅ installOverrides() succeeded');

        // 4) registerHooks()
        $this->logToFile('Step 4: registering hooks');
        if (!$this->registerHooks()) {
            $this->logToFile('❌ registerHooks() failed');
            return false;
        }
        $this->logToFile('✅ registerHooks() succeeded');

        // Fin de l’installation
        $this->logToFile('=== Install completed successfully ===');
        return true;
    }

    // public function install()
    // {
    //     if (!parent::install()) {
    //         return false;
    //     }

    //     if (!$this->installDb()) {
    //         return false;
    //     }

    //     if (!$this->installOverrides()) {
    //         return false;
    //     }

    //     return $this->registerHooks();
    // }

    private function installDb(): bool
    {
        // Charger les requêtes
        $queries = include __DIR__ . '/sql/install.php';
        if (!is_array($queries)) {
            return false;
        }

        // Désactiver les FK
        Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($queries as $i => $query) {
            $res = Db::getInstance()->execute($query);
            if (!$res) {
                $err = Db::getInstance()->getMsgError();
                Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 1');
                return false;
            }
        }

        // Reactiver les FK
        Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 1');

        // Configuration globale
        Configuration::updateValue('MINCARTFEE_MIN_AMOUNT', 50.00);
        Configuration::updateValue('MINCARTFEE_FEE_NAME', 'Minimum Order Fee');

        return true;
    }
    
    public function installOverrides(): bool
    {
        try {
            // Parent installOverrides() parcourt modules/yourModule/override
            if (!parent::installOverrides()) {
                PrestaShopLogger::addLog(
                    "Module {$this->name}: échec de l’installation des overrides",
                    3, null, 'Module', (int)$this->id
                );
                return false;
            }
            // Regénère l’index des classes pour prendre en compte les nouveaux overrides
            @unlink(_PS_ROOT_DIR_ . '/var/cache/class_index.php');
            return true;
        } catch (\Exception $e) {
            PrestaShopLogger::addLog(
                "Module {$this->name}: exception installOverrides() — " . $e->getMessage(),
                3, null, 'Module', (int)$this->id
            );
            return false;
        }
    }

    public function uninstallOverrides(): bool
    {
        try {
            if (!parent::uninstallOverrides()) {
                PrestaShopLogger::addLog(
                    "Module {$this->name}: échec de la désinstallation des overrides",
                    3, null, 'Module', (int)$this->id
                );
                return false;
            }
            @unlink(_PS_ROOT_DIR_ . '/var/cache/class_index.php');
            return true;
        } catch (\Exception $e) {
            PrestaShopLogger::addLog(
                "Module {$this->name}: exception uninstallOverrides() — " . $e->getMessage(),
                3, null, 'Module', (int)$this->id
            );
            return false;
        }
    }

    private function registerHooks(): bool
    {
        
        // 1) Créer les custom hooks s’ils n’existent pas
        $customHooks = [
            'displayMinimumCartFee'     => 'Display minimum cart fee on invoice',
            'displayMinimumCartFeeSlip' => 'Display minimum cart fee on credit slip',
        ];
        foreach ($customHooks as $name => $title) {
            if (!Hook::getIdByName($name)) {
                $hook = new Hook();
                $hook->name        = pSQL($name);
                $hook->title       = pSQL($title);
                $hook->description = pSQL("$title (custom PDF hook)");
                $hook->add();
            }
        }

        // 2) Enregistrer le module sur ces hooks
        foreach (array_keys($customHooks) as $hookName) {
            if (!$this->registerHook($hookName)) {
                return false;
            }
        }

        $hooks = [
            'actionCartSave',
            'displayHeader',
            'actionEmailSendBefore',
            'actionOrderStatusUpdate',
            'actionValidateOrder',
            'displayOrderConfirmation',
        ];

        foreach ($hooks as $h) {
            if (!$this->registerHook($h)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall(): bool
    {
        try {
            // 1) DROP SQL tables
            $result = include __DIR__ . '/sql/uninstall.php';
            if (!$result) {
                return false;
            }

            // 2) Unregister hooks
            $hooks = [
                'displayMinimumCartFee',
                'displayMinimumCartFeeSlip',
                'actionCartSave',
                'displayHeader',
                'actionEmailSendBefore',
                'actionOrderStatusUpdate',
                'actionValidateOrder',
                'displayOrderConfirmation',
            ];
            foreach ($hooks as $hookName) {
                $hookId = Hook::getIdByName($hookName);
                if ($hookId) {
                    if ($this->unregisterHook($hookName)) {
                    } else {
                        return false;
                    }
                }
            }

            // 3) Uninstall overrides
            if (!$this->uninstallOverrides()) {
                return false;
            }

            // 4) Delete configuration values
            $del1 = Configuration::deleteByName('MINCARTFEE_MIN_AMOUNT');
            $del2 = Configuration::deleteByName('MINCARTFEE_FEE_NAME');

            // 5) Parent uninstall
            if (!parent::uninstall()) {
                return false;
            }
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
    // public function uninstall(): bool
    // {
    //     try {
    //         // 1) DROP tables SQL
    //         // if (!($result = include __DIR__.'/sql/uninstall.php')) {
    //         //     return false;
    //         // }

    //         // 2) Debug avant la boucle
    //         $hooks = [
    //             'displayMinimumCartFee',
    //             'displayMinimumCartFeeSlip',
    //             'actionCartSave',
    //             'displayHeader',
    //             'actionEmailSendBefore',
    //             'actionOrderStatusUpdate',
    //             'actionValidateOrder',
    //             'displayOrderConfirmation',
    //         ];

    //         // 3) Boucle avec log de chaque nom
    //         foreach ($hooks as $hookName) {
    //             $hookId = Hook::getIdByName($hookName);
    //             if ($hookId) {
    //                 if (!$this->unregisterHook($hookName)) {
    //                     return false;
    //                 }
    //             }
    //         }

    //         if (!$this->uninstallOverrides()) {
    //             return false;
    //         }

    //         // 4) Suppression des configurations
    //         Configuration::deleteByName('MINCARTFEE_MIN_AMOUNT');
    //         Configuration::deleteByName('MINCARTFEE_FEE_NAME');

    //         // 5) Désinstallation parent
    //         if (!parent::uninstall()) {
    //             return false;
    //         }

    //         return true;
    //     } catch (Exception $e) {
    //         return false;
    //     }
    // }

    public function getContent()
    {

        if (Tools::getIsset('deleteminimumcartfee_config') && Tools::getIsset('id_minimumcartfee_config')) {
            $idToDelete = (int) Tools::getValue('id_minimumcartfee_config');
    
            return $this->handleDelete($idToDelete) . $this->getContent(); // Reload content after deletion
        }

        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        
        $activeTab = Tools::getValue('tab', 'form');
        $output = '';
        
        // Add tabs navigation
        $output .= '<div class="panel">';
        $output .= '<div class="panel-heading">';
        $output .= '<ul class="nav nav-tabs">';
        $output .= '<li class="'.($activeTab !== 'list' ? 'active' : '').'">';
        $output .= '<a href="#tab-form" data-toggle="tab">'.$this->l('Create/Edit Fee').'</a>';
        $output .= '</li>';
        $output .= '<li class="'.($activeTab === 'list' ? 'active' : '').'">';
        $output .= '<a href="#tab-list" data-toggle="tab">'.$this->l('Existing Fees').'</a>';
        $output .= '</li>';
        $output .= '</ul>';
        $output .= '</div>';
        
        $output .= '<div class="tab-content">';
        $output .= '<div class="tab-pane '.($activeTab !== 'list' ? 'active' : '').'" id="tab-form">';
        
        if (Tools::isSubmit('submitMinimumCartFee')) {
            $output .= $this->handleSubmit();
        }
        $output .= $this->renderForm();
        
        $output .= '</div>';
        $output .= '<div class="tab-pane '.($activeTab === 'list' ? 'active' : '').'" id="tab-list">';
        $output .= $this->renderFeeList();
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }

    private function handleDelete($idConfig)
    {

        Configuration::deleteByName('MINCARTFEE_MIN_AMOUNT');
        Configuration::deleteByName('MINCARTFEE_FEE_NAME');

        if (!$idConfig) {
            return $this->displayError($this->l('Invalid configuration ID.'));
        }
    
        // Delete from all related tables
        Db::getInstance()->delete('minimumcartfee_product', 'id_minimumcartfee_config = ' . (int)$idConfig);
        Db::getInstance()->delete('minimumcartfee_category', 'id_minimumcartfee_config = ' . (int)$idConfig);
        Db::getInstance()->delete('minimumcartfee_config', 'id_minimumcartfee_config = ' . (int)$idConfig);
    
        // Redirect to module config page, tab=list, with success flag
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules') .
            '&configure=' . $this->name .
            '&tab=list&deleted=1'
        );
    }
    

    private function renderForm()
    {
        $idShop = (int)$this->context->shop->id;
        $config = Db::getInstance()->getRow('
            SELECT * FROM `'._DB_PREFIX_.'minimumcartfee_config`
            WHERE id_shop = '.$idShop
        );
    
        // Get categories for multiselect
        $categories = Category::getCategories($this->context->language->id, false, false);
        $categoryOptions = [];
        foreach ($categories as $category) {
            if ($category['id_category'] != 1) { // Skip root category
                $categoryOptions[] = [
                    'id' => $category['id_category'],
                    'name' => $category['name']
                ];
            }
        }
    
        // Get selected categories
        $selectedCategories = [];
        if ($config) {
            $selectedCategories = Db::getInstance()->executeS('
                SELECT id_category FROM `'._DB_PREFIX_.'minimumcartfee_category`
                WHERE id_minimumcartfee_config = '.(int)$config['id_minimumcartfee_config']
            );
            $selectedCategories = array_column($selectedCategories, 'id_category');
        }
    
        // Get selected products
        $selectedProducts = [];
        if ($config) {
            $selectedProducts = Db::getInstance()->executeS('
                SELECT id_product FROM `'._DB_PREFIX_.'minimumcartfee_product`
                WHERE id_minimumcartfee_config = '.(int)$config['id_minimumcartfee_config']
            );
            $selectedProducts = array_column($selectedProducts, 'id_product');
        }
    
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Minimum Cart Fee Settings'),
                    'icon' => 'icon-cog'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Fee Name'),
                        'name' => 'MINCARTFEE_FEE_NAME',
                        'required' => true,
                        'desc' => $this->l('This will be displayed to customers')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Minimum Cart Amount'),
                        'name' => 'MINCARTFEE_MIN_AMOUNT',
                        'required' => true,
                        'suffix' => $this->context->currency->sign,
                        'desc' => $this->l('Fee will be applied if cart is below this amount')
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Apply to Categories'),
                        'name' => 'MINCARTFEE_CATEGORIES[]',
                        'multiple' => true,
                        'options' => [
                            'query' => $categoryOptions,
                            'id' => 'id',
                            'name' => 'name'
                        ],
                        'desc' => $this->l('Select categories where fee should apply')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Apply to Specific Products'),
                        'name' => 'MINCARTFEE_PRODUCTS',
                        'desc' => $this->l('Comma-separated product IDs (e.g. 1,5,8)')
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-primary pull-right'
                ]
            ]
        ];
    
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = 'id_minimumcartfee_config';
        $helper->submit_action = 'submitMinimumCartFee';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
    
        $helper->fields_value = [
            'MINCARTFEE_FEE_NAME' => $config['fee_name'] ?? '',
            'MINCARTFEE_MIN_AMOUNT' => $config['min_amount'] ?? '',
            'MINCARTFEE_CATEGORIES[]' => $selectedCategories,
            'MINCARTFEE_PRODUCTS' => implode(',', $selectedProducts)
        ];
    
        return $helper->generateForm([$fieldsForm]);
    }
    
    private function handleSubmit()
    {
        $idShop = (int)$this->context->shop->id;
        $feeName = Tools::getValue('MINCARTFEE_FEE_NAME');
        $minAmount = (float)Tools::getValue('MINCARTFEE_MIN_AMOUNT');
        $categories = Tools::getValue('MINCARTFEE_CATEGORIES', []);
        $products = array_filter(explode(',', Tools::getValue('MINCARTFEE_PRODUCTS', '')));
    
        // Validate inputs
        if (empty($feeName)) {
            return $this->displayError($this->l('Fee name is required'));
        }
        if ($minAmount <= 0) {
            return $this->displayError($this->l('Minimum amount must be greater than 0'));
        }
    
        // Check if config exists
        $config = Db::getInstance()->getRow(
            'SELECT id_minimumcartfee_config FROM `'._DB_PREFIX_.'minimumcartfee_config`'
        );
    
        Configuration::updateValue('MINCARTFEE_MIN_AMOUNT', $minAmount);
        Configuration::updateValue('MINCARTFEE_FEE_NAME', $feeName);

        if ($config) {
            // Update existing config
            Db::getInstance()->update('minimumcartfee_config', [
                'fee_name' => pSQL($feeName),
                'min_amount' => $minAmount
            ], 'id_minimumcartfee_config = '.(int)$config['id_minimumcartfee_config']);
    
            $idConfig = (int)$config['id_minimumcartfee_config'];
        } else {
            // Create new config
            Db::getInstance()->insert('minimumcartfee_config', [
                'fee_name' => pSQL($feeName),
                'min_amount' => $minAmount,
                'id_shop' => $idShop
            ]);
            $idConfig = (int)Db::getInstance()->Insert_ID();
        }
    
        // Update categories
        Db::getInstance()->delete('minimumcartfee_category', 'id_minimumcartfee_config = '.$idConfig);
        if (!empty($categories)) {
            foreach ($categories as $idCategory) {
                Db::getInstance()->insert('minimumcartfee_category', [
                    'id_minimumcartfee_config' => $idConfig,
                    'id_category' => (int)$idCategory
                ]);
            }
        }
    
        // Update products
        Db::getInstance()->delete('minimumcartfee_product', 'id_minimumcartfee_config = '.$idConfig);
        if (!empty($products)) {
            foreach ($products as $idProduct) {
                Db::getInstance()->insert('minimumcartfee_product', [
                    'id_minimumcartfee_config' => $idConfig,
                    'id_product' => (int)$idProduct
                ]);
            }
        }
    
        return $this->displayConfirmation($this->l('Settings updated successfully'));
    }
    
    private function renderFeeList()
    {
        $idShop = (int)$this->context->shop->id;
        $fees = Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'minimumcartfee_config`
            WHERE id_shop = '.$idShop
        );
    
        if (empty($fees)) {
            return '<div class="alert alert-info">'.$this->l('No fee configurations found.').'</div>';
        }
    
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->identifier = 'id_minimumcartfee_config';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->title = $this->l('Existing Fees');
        $helper->table = 'minimumcartfee_config';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
    
        $fields_list = [
            'id_minimumcartfee_config' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'fee_name' => [
                'title' => $this->l('Fee Name'),
                'align' => 'left'
            ],
            'min_amount' => [
                'title' => $this->l('Minimum Amount'),
                'align' => 'right',
                'type' => 'price',
                'currency' => true
            ]
        ];
    
        return $helper->generateList($fees, $fields_list);
    }

    // public function logToFile($message)
    // {
    //     $logFile = __DIR__ . '/log.txt';
    //     $timestamp = date('Y-m-d H:i:s');
    //     $formattedMessage = sprintf("[%s] %s\n", $timestamp, $message);

    //     file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    // }

    public function hookDisplayOrderConfirmation($params)
    {
        /** @var Order $order */
        $order = $params['order'];

        if (empty($order->id)) {
            return '';
        }

        // Get fee info for this order
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT fee_label, fee_amount
            FROM '._DB_PREFIX_.'minimumcartfee_order
            WHERE id_order = '.(int)$order->id
        );

        if (!$row || (float)$row['fee_amount'] <= 0) {
            return '';
        }

        $feeLabel = $row['fee_label'];
        $feeAmount = (float)$row['fee_amount'];
        $formattedAmount = Tools::displayPrice($feeAmount, $this->context->currency);

        // Assign to template
        $this->context->smarty->assign([
            'custom_fee_label' => $feeLabel,
            'custom_fee_amount' => $formattedAmount,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/order_confirmation_fee.tpl');
    }

    public function hookDisplayMinimumCartFee(array $params): string
    {
        /** @var OrderInvoice $invoiceObj */
        $invoiceObj = $params['object'];

        if (empty($invoiceObj->id_order)) {
            return '';
        }

        $orderId = (int)$invoiceObj->id_order;

        // Check if order is canceled
        $order = new Order($orderId);

        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        // ➤ Fetch fee info from your table
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT fee_label, fee_amount
            FROM '._DB_PREFIX_.'minimumcartfee_order
            WHERE id_order = '.(int)$orderId
        );

        if (!$row) {
            return '';
        }

        $feeLabel = $row['fee_label'];
        $feeAmount = (float)$row['fee_amount'];

        if ($feeAmount <= 0) {
            return '';
        }

        $formatted = Tools::displayPrice($feeAmount, (int)$invoiceObj->id_currency);
        // Return the HTML block
        return <<<HTML
            <tr class="bold">
                <td class="grey">
                    {$feeLabel}
                </td>
                <td class="white">
                    {$formatted}
                </td>
            </tr>
        HTML;
    }

    public function hookDisplayMinimumCartFeeSlip(array $params): string
    {
        /** @var OrderSlip $orderSlip */
        $orderSlip = $params['object'] ?? null;
        if (!$orderSlip || !Validate::isLoadedObject($orderSlip)) {
            return '';
        }
        $order = new Order((int)$orderSlip->id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT fee_label, fee_amount
            FROM ' . _DB_PREFIX_ . 'minimumcartfee_order
            WHERE id_order = ' . (int)$order->id
        );
        if (!$row || (float)$row['fee_amount'] <= 0) {
            // On indique zéro pour ne pas break le template
            // Context::getContext()->smarty->assign('minimum_cart_fee_amount', 0);
            return '';
        }

        $feeLabel  = $row['fee_label'];
        $feeAmount = (float)$row['fee_amount'] - $orderSlip->total_products_tax_incl;
        $formatted = Tools::displayPrice($feeAmount, (int)$order->id_currency);

        // ① On passe la valeur brute à Smarty
        // Context::getContext()->smarty->assign('minimum_cart_fee_amount', $feeAmount);

        // ② On retourne la ligne HTML
        return <<<HTML
    <tr>
    <td class="grey">{$feeLabel}</td>
    <td class="white">- {$formatted}</td>
    </tr>
    HTML;
    }

    public function hookActionEmailSendBefore(array $params)
    {
        $feeLabel = 'Frais de commande';
        $feeAmount = 0;
        $formattedAmount = '0.00 €';

        // 1. If template is 'order_conf' → use order DB
        if (isset($params['template']) && $params['template'] === 'order_conf' && isset($params['templateVars']['{id_order}'])) {
            $orderId = (int)$params['templateVars']['{id_order}'];

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
                'SELECT fee_label, fee_amount FROM '._DB_PREFIX_.'minimumcartfee_order WHERE id_order = '.(int)$orderId
            );

            if ($row && (float)$row['fee_amount'] > 0) {
                $feeLabel = $row['fee_label'];
                $feeAmount = (float)$row['fee_amount'];
                $order = new Order($orderId);
                $currency = new Currency($order->id_currency);
                $formattedAmount = Tools::displayPrice($feeAmount, $currency);
            }
        }

        // 2. If template is 'new_order' → use cart-based calculation
        elseif (isset($params['template']) && $params['template'] === 'new_order' && isset($params['cart'])) {
            $cart = $params['cart'];

            $feeAmount = $this->calculateMinimumCartFee($cart);
            if ($feeAmount > 0) {
                $feeLabel = Configuration::get('MINCARTFEE_FEE_NAME', $cart->id_lang) ?: 'Frais de commande';
                $currency = new Currency($cart->id_currency);
                $formattedAmount = Tools::displayPrice($feeAmount, $currency);
            }
        }

        // 3. Inject values into templateVars (default or computed)
        $params['templateVars']['{custom_fee_label}'] = $feeLabel;
        $params['templateVars']['{custom_fee_value}'] = $formattedAmount;

        return $params;
    }

    public function hookActionValidateOrder($params)
    {    
        if (empty($params['order']) || empty($params['cart'])) {
            return;
        }
    
        /** @var Order $order */
        $order = $params['order'];
    
        /** @var Cart $cart */
        $cart = $params['cart'];
    
        // Calculate the minimum fee
        $fee = $this->calculateMinimumCartFee($cart);
    
        if ($fee > 0) {
            $feeLabel = Configuration::get('MINCARTFEE_FEE_NAME');
    
            Db::getInstance()->insert('minimumcartfee_order', [
                'id_order'  => (int)$order->id,
                'fee_label' => pSQL($feeLabel),
                'fee_amount'=> (float)$fee,
            ]);

        }
        
    }

    public function hookActionCartSave($params)
    {
        $cart = Context::getContext()->cart;

        if (!Validate::isLoadedObject($cart)) {
            return;
        }

        $this->applyMinimumFee($cart, false);
    }

    /**
     * Before PrestaShop generates the PDF, adjust each OrderInvoice
     * to include our minimum‑cart fee in the shipping & total lines.
     */

    public function hookDisplayHeader($params)
    {
        $this->logToFile('hookDisplayHeader called');
        if ($this->context->controller->controller_type !== 'front') {
            return;
        }

        // $fee = $this->calculateMinimumCartFee($this->context->cart);
        // if ($fee <= 0) {
        //     return;
        // }

        // 1) Add your JS file
        $this->context->controller->addJS($this->_path . 'views/js/minimumcartfee.js');

        // 2) Expose the fee values and the AJAX endpoint
        Media::addJsDef([
            // 'minCartFee'        => $fee,
            'minCartFeeLabel'   => addslashes(Configuration::get('MINCARTFEE_FEE_NAME')),
            // 'minCartFeeDisplay' => addslashes(Tools::displayPrice($fee)),
            'minCartFeeUrl'     => $this->context->link->getModuleLink(
                $this->name,
                'ajaxfee',
                [], 
                true
            ),
        ]);
    }

      
    
    protected function applyMinimumFee(Cart $cart, $shouldUpdate = true)
    {

        if (!Validate::isLoadedObject($cart)) {
            return;
        }

        $minAmount = (float) Configuration::get('MINCARTFEE_MIN_AMOUNT');
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        $context = Context::getContext();
    
        if ($cartTotal < $minAmount) {
            $feeAmount = $minAmount - $cartTotal;
            $cart->additional_shipping_cost = $feeAmount;
        } else {
            $cart->additional_shipping_cost = 0;
        }
    
        if ($shouldUpdate) {
            $cart->update();
        }
    }


    public function calculateMinimumCartFee(Cart $cart)
    {
        $products = $cart->getProducts();

        $minAmount = (float) Configuration::get('MINCARTFEE_MIN_AMOUNT');
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        $allSamples = $this->checkIsSample($cart, $products); // Capture the result

        if ($cartTotal <= 0) {
            return 0;
        }

        // ➤ If all products are samples, NO FEE
        if ($allSamples) {
            return 0;
        }

        // ➤ Otherwise, apply fee if needed
        if ($cartTotal < $minAmount) {
            $fee = $minAmount - $cartTotal;
            return $fee;
        }

        return 0;
    }

    protected function checkIsSample(Cart $cart, array $products)
    {
        $sampleProducts = [];

        if (Module::isEnabled('wksampleproduct')) {
            $sampleModule = Module::getInstanceByName('wksampleproduct');

            if (!empty($products)) {
                $sampleInfos = $sampleModule->getSampleCartInformations(
                    $cart->id,
                    array_column($products, 'id_product')
                );
                $sampleProducts = $sampleInfos['samples'] ?? [];
            }
        }

        if (empty($products)) {
            return false; // By default: no products = no samples
        }

        foreach ($products as $product) {
            $productKey = $product['id_product'] . '_' . (int)$product['id_product_attribute'];
            $isSample = in_array($productKey, $sampleProducts);

            if (!$isSample) {
                // As soon as one is NOT a sample, stop
                return false;
            }
        }

        // If we completed loop: all were samples
        return true;
    }

    
}
