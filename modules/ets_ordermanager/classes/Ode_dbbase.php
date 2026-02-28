<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_') || !defined('_ETS_ODE_MODULE_'))
    exit;

class Ode_dbbase
{
    protected static $instance;
    public static function getInstance($module = null)
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ode_dbbase($module);
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_ODE_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function installDb()
    {
        $fieldsorders = Db::getInstance()->ExecuteS('DESCRIBE ' . _DB_PREFIX_ . 'orders');
        $check_add_order_note = true;
        $check_add_deleted = true;
        foreach ($fieldsorders as $field) {
            if ($field['Field'] == 'order_note') {
                $check_add_order_note = false;
                if (!$check_add_deleted)
                    break;
            }
            if ($field['Field'] == 'deleted') {
                $check_add_deleted = false;
                if (!$check_add_order_note)
                    break;
            }
        }
        if ($check_add_order_note)
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD  `order_note` text default ""');
        if ($check_add_deleted)
            Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD  `deleted` INT(1) default "0"');
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_export_order_rule`( 
            `id_ets_export_order_rule` INT(11) NOT NULL AUTO_INCREMENT , 
            `exported_fields` TEXT NULL ,
            `date_type` VARCHAR(33) NULL,
            `send_file_via_email` INT(1),            
            `send_file_schedule` VARCHAR(50),            
            `send_file_time_hours` VARCHAR(20),
            `send_file_time_weeks` VARCHAR(20),
            `send_file_time_months` VARCHAR(20),
            `send_file_date` DATETIME,
                        
            `export_to_server1` INT(1),
            `directory_path1` VARCHAR(254) NULL,
            `server1_schedule` VARCHAR(50),            
            `server1_time_hours` VARCHAR(20),
            `server1_time_weeks` VARCHAR(20),
            `server1_time_months` VARCHAR(20),
            `server1_date` DATETIME,
            
            `export_to_server2` INT(1),
            `global_ftp` INT(1),
            `host` VARCHAR(50) NULL,
            `username` VARCHAR(150) NULL,
            `port` VARCHAR(6) NULL,
            `password` VARCHAR(150) NULL,
            `directory_path2` VARCHAR(254) NULL,
            `server2_schedule` VARCHAR(50),            
            `server2_time_hours` VARCHAR(20),
            `server2_time_weeks` VARCHAR(20),
            `server2_time_months` VARCHAR(20),
            `server2_date` DATETIME,
            `delete_exported_files` VARCHAR(20),
            `from_date` date,
            `to_date` date,
            `day_before` int (11) NULL,
            `order_status` TEXT NULL ,
            `customer_group` VARCHAR(254) NULL , 
            `specific_customer` text NULL ,
            `specific_order` text NULL ,  
            `id_country` TEXT NULL , 
            `id_state` INT(11) NULL ,
            `order_carrier` text NULL , 
            `payment_method` text NULL , 
            `manufacturer` text NULL, 
            `supplier` text NULL, 
            `category` text NULL, 
            `specific_product` text NULL, 
            `spent_from` VARCHAR(15), 
            `spent_to` VARCHAR(15),
            `file_format` VARCHAR(254) NULL,
            `file_name_prefix` VARCHAR(254) NULL,
            `file_name_incl_name_rule` INT(1),
            `sort_by` VARCHAR(254) NULL,
            `convert_in_currency` INT(11),
            `receivers_mail` text,
            `position` int (11), 
            `send_file_filter` VARCHAR(222),
            `server1_filter` VARCHAR(222),
            `server2_filter` VARCHAR(222),                      
        PRIMARY KEY (`id_ets_export_order_rule`)) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8')
            && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_export_order_rule_lang` (
            `id_ets_export_order_rule` INT(11) NOT NULL ,
            `id_lang` INT(11) NOT NULL , 
            `name` VARCHAR(254) NOT NULL,
            `title_mail` text,
            `description_mail` text, 
        PRIMARY KEY (`id_ets_export_order_rule`, `id_lang`)) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8')
            && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_export_order_rule_shop` (
            `id_ets_export_order_rule` INT(11) NOT NULL ,
            `id_shop` INT(11) NOT NULL ,
        PRIMARY KEY (`id_ets_export_order_rule`, `id_shop`)) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8')
            && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_odm_customer_login` ( 
            `id_customer` INT(11) NOT NULL , 
            `token` VARCHAR(50) NOT NULL , 
            `date_add` DATETIME NOT NULL ,
             PRIMARY KEY (`id_customer`)) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8'
            ) && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_odm_order_detail_refund` ( 
            `id_order_detail` INT(11) NOT NULL , 
            `refund` INT(1) , 
             INDEX (`id_order_detail`,`refund`)) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8'
            );
    }
    public static function uninstallDb()
    {
        Configuration::deleteByName('ETS_ORDERMANAGE_ARRANGE_LIST');
        @unlink(_PS_ETS_ODE_LOG_DIR_ . '/ode_cronjob.log');
        return Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_export_order_rule')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_export_order_rule_lang')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_export_order_rule_shop');
    }
    public static function getProductByQuery($query,$excludedProductIds=null,$excludeVirtuals=false,$exclude_packs=false)
    {
        if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
            $imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) ' . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1');
        } else {
            $imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ') ';
        }
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
            		FROM `' . _DB_PREFIX_ . 'product` p
            		' . Shop::addSqlAssociation('product', 'p') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            		' . pSQL($imgLeftJoin) . ' 
            		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)Context::getContext()->language->id . ')
            		WHERE ' . ($excludedProductIds ? 'p.`id_product` NOT IN(' . pSQL(implode(',', array_map('intval',$excludedProductIds) )) . ') AND ' : '') . ' (pl.name LIKE "%' . pSQL($query) . '%" OR p.reference LIKE "%' . pSQL($query) . '%" OR p.id_product = ' . (int)$query . ')' .
            ($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ($imgLeftJoin ? 'AND image_shop.cover = 1' : '') . ' GROUP BY p.id_product';
        return Db::getInstance()->executeS($sql);
    }
    public static function getCombinationsByIdProduct($id_product,$excludeIds=false)
    {
        $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name, NULL as `attribute`, a.`id_attribute`
            					FROM `' . _DB_PREFIX_ . 'product_attribute` pa
            					' . Shop::addSqlAssociation('product_attribute', 'pa') . '
            					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
            					LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)Context::getContext()->language->id . ')
            					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)Context::getContext()->language->id . ')
            					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
            					WHERE pa.`id_product` = ' . (int)$id_product . ($excludeIds ? ' AND NOT FIND_IN_SET(CONCAT(pa.`id_product`,"-", IF(pa.`id_product_attribute` IS NULL OR od.`product_id` = '.(int)Configuration::get('PH_EXTEND_ID_PRODUCT').',0,pa.`id_product_attribute`)), "' . pSQL($excludeIds) . '")' : '') . '
            					GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
            					ORDER BY pa.`id_product_attribute`';
        return Db::getInstance()->executeS($sql);
    }
    public static function getCombinationImageById($id_product_attribute, $id_lang)
    {
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            return Product::getCombinationImageById($id_product_attribute, $id_lang);
        } else {
            if (!Combination::isFeatureActive() || !$id_product_attribute) {
                return false;
            }
            $result = Db::getInstance()->executeS('
                SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
                WHERE pai.`id_product_attribute` = ' . (int)$id_product_attribute . ' AND il.`id_lang` = ' . (int)$id_lang . ' ORDER by i.`position` LIMIT 1'
            );
            if (!$result) {
                return false;
            }
            return $result[0];
        }
    }
    public static function getFeeOrders($id_order)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_paymentmethod_order` WHERE id_order=' . (int)$id_order);
    }
    public static function addFeeOrder($id_order,$payment_fee)
    {
        if($payment_fee)
            return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_paymentmethod_order` (id_paymentmethod,id_order,method_name,fee) values("' . (int)$payment_fee['id_paymentmethod'] . '","' . (int)$id_order . '","' . pSQL($payment_fee['method_name']) . '","' . (float)$payment_fee['fee'] . '")');
    }
    public static function restoreOrder($id_order)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` set deleted=0 WHERE id_order=' . (int)$id_order);
    }
    public static function deleteOrderTrash($id_order)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` set deleted=1 WHERE id_order=' . (int)$id_order);
    }
    public static function getOrderTrash()
    {
        return Db::getInstance()->executeS('SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` WHERE deleted=1');
    }
    public static function getMaxIdOrderStateByOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT max(id_order_state) FROM `' . _DB_PREFIX_ . 'order_history` WHERE id_order=' . (int)$id_order);
    }

    /**
     * @param Customer $customer
     * @return string
     */
    public static function addTokenLogin($customer)
    {
        $token = MD5(time() . '-' . $customer->id . '-' . $customer->passwd);
        if (Db::getInstance()->getRow('SELECT * FROM  `' . _DB_PREFIX_ . 'ets_odm_customer_login` WHERE id_customer=' . (int)$customer->id))
            Db::getInstance()->execute('UPDATE  `' . _DB_PREFIX_ . 'ets_odm_customer_login` SET token="' . pSQL($token) . '",date_add="' . pSQL(date('Y-m-d H:i:s')) . '" WHERE id_customer=' . (int)$customer->id);
        else
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_odm_customer_login` (id_customer,token,date_add) VALUES (' . (int)$customer->id . ',"' . pSQL($token) . '","' . pSQL(date('Y-m-d H:i:s')) . '")');
        return $token;
    }
    public function updateOrderInLine($id_order,$table,$primary_key,$key_change,$value_change,$key_value,&$assign ,&$errors)
    {
        if ($table == 'address') {
            $order = new Order($id_order);
            $address = new Address($order->{$primary_key});
            $address->{$key_change} = $value_change;
            unset($address->id);
            $address->deleted = 1;
            if ($address->add()) {
                $order->{$primary_key} = $address->id;
                $cart = new Cart($order->id_cart);
                $cart->{$primary_key} = $address->id;
                $cart->update();
                $order->update();
            }
            if ($key_change == 'id_country') {
                $country = new Country($value_change, Context::getContext()->language->id);
                $assign['value_changed'] = $country->name;
            } else {
                $assign['value_changed'] = $value_change;
            }
        } else {
            if ($key_change == 'current_state') {
                $order_state = new OrderState($value_change);
                if (!Validate::isLoadedObject($order_state)) {
                    $errors[] = $this->l('The new order status is invalid.');
                } else {
                    $order = new Order($id_order);
                    $current_order_state = $order->getCurrentOrderState();
                    if (!$current_order_state || $current_order_state->id != $order_state->id) {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)Context::getContext()->employee->id;
                        $use_existings_payment = false;
                        if (!$order->hasInvoice()) {
                            $use_existings_payment = true;
                        }
                        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);
                        // Save all changes
                        $history->add();
                        $assign['badge_success'] = $order_state->logable;
                        $assign['PDFIcons'] = version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? Module::getInstanceByName('ets_ordermanager')->printPDFIcon($id_order) : Context::getContext()->controller->printPDFIcons($id_order, false);

                    } else {
                        $errors[] = $this->l('The order has already been assigned this status.');
                    }
                }
                $assign['value_changed'] = $order_state->name[Context::getContext()->language->id];
                $assign['value_style'] = 'background-color:' . $order_state->color . ';color:' . (Tools::getBrightness($order_state->color) < 128 ? 'white' : '#383838') . ';';

            } else {
                $assign['value_changed'] = $value_change . ($key_change == 'weight' ? ' ' . Configuration::get('PS_WEIGHT_UNIT') : '');
                if ($key_change == 'amount' && $table == 'order_payment') {
                    $order_payment = new OrderPayment($key_value);
                    if ($order_payment->id_currency)
                        $currency = new Currency($order_payment->id_currency);
                    else
                        $currency = Context::getContext()->currency;
                    $assign['value_changed'] = Tools::displayPrice($value_change, $currency);
                }
                if ($key_change == 'shipping_cost_tax_excl' || $key_change == 'shipping_cost_tax_incl') {
                    if (!Configuration::get('PS_ORDER_RECALCULATE_SHIPPING') && version_compare(_PS_VERSION_, '1.7', '>=')) {
                        $errors[] = $this->l('You do not have permission to change shipping code');
                    } else {
                        $order = new Order($id_order);
                        if ($order->id_currency)
                            $currency = new Currency($order->id_currency);
                        else
                            $currency = Context::getContext()->currency;
                        $cart = new Cart($order->id_cart);
                        if (!$cart->id)
                            $errors[] = $this->l('Cart') . ' #' . $order->id_cart . ' ' . $this->l('does not exists');
                        else {
                            $order_carrier = new OrderCarrier($key_value);
                            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                                {
                                    $carrier_tax = $cart->getAverageProductsTaxRate();
                                }
                            } else {
                                $address = Address::initialize((int)$order->id_address_delivery);
                                $carrier = new Carrier($order->id_carrier);
                                $carrier_tax = $carrier->getTaxesRate($address);
                            }

                            if ($key_change == 'shipping_cost_tax_incl') {
                                $shipping_cost_tax_excl = (float)$value_change / (1 + $carrier_tax / 100);
                            } elseif ($key_change == 'shipping_cost_tax_excl') {
                                $shipping_cost_tax_excl = (float)$value_change;
                            }
                            if ($order_carrier->id_carrier) {

                                $new_carrier = new Carrier($order_carrier->id_carrier);
                                $id_tax_rules_group = (int)Db::getInstance()->getValue('SELECT id_tax_rules_group FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' . (int)$order_carrier->id_carrier);
                                $carrier_group = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop` WHERE id_carrier=' . (int)$order_carrier->id_carrier . ' AND id_shop=' . (int)Context::getContext()->shop->id);
                                $id_reference = $new_carrier->id_reference;
                                unset($new_carrier->id);
                                $new_carrier->deleted = 1;
                                $new_carrier->active = 1;
                                $new_carrier->shipping_method = 2;
                                $new_carrier->is_free = 0;
                                $new_carrier->shipping_handling = 0;
                                $new_carrier->shipping_external = 0;
                                if ($new_carrier->add()) {

                                    if ($id_tax_rules_group) {
                                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`(`id_carrier`, `id_tax_rules_group`, `id_shop`) values("' . (int)$new_carrier->id . '","' . (int)$id_tax_rules_group . '","' . (int)Context::getContext()->shop->id . '")');
                                    } elseif ($carrier_group)
                                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`(`id_carrier`, `id_tax_rules_group`, `id_shop`) values("' . (int)$new_carrier->id . '","' . (int)$carrier_group['id_tax_rules_group'] . '","' . (int)$carrier_group['id_shop'] . '")');
                                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'carrier` set id_reference="' . (int)$id_reference . '",id_tax_rules_group="' . (int)$id_tax_rules_group . '" WHERE id_carrier=' . (int)$new_carrier->id);
                                    $rangPrice = new RangePrice();
                                    $rangPrice->id_carrier = $new_carrier->id;
                                    $rangPrice->delimiter1 = 0;
                                    $rangPrice->delimiter2 = 9999999999;
                                    if ($rangPrice->add()) {
                                        $zones = Db::getInstance()->executeS('SELECT id_zone FROM ' . _DB_PREFIX_ . 'zone');
                                        if ($zones) {
                                            foreach ($zones as $zone) {
                                                $delivery = new Delivery();
                                                $delivery->id_carrier = (int)$new_carrier->id;
                                                $delivery->id_range_price = (int)$rangPrice->id;
                                                $delivery->id_range_weight = 0;
                                                $delivery->id_zone = (int)$zone['id_zone'];
                                                $delivery->price = Tools::ps_round((float)$shipping_cost_tax_excl / $currency->conversion_rate, 2);
                                                $delivery->id_shop = NULL;
                                                $delivery->id_shop_group = NULL;
                                                $delivery->add();
                                                Db::getInstance()->execute('update `' . _DB_PREFIX_ . 'delivery` set id_shop = null, id_shop_group=null where id_delivery=' . (int)$delivery->id);
                                            }
                                        }
                                    }
                                    $order->id_carrier = $new_carrier->id;
                                    $order->update();
                                }
                            }
                            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_invoice_tax` WHERE id_order_invoice=' . (int)$order_carrier->id_order_invoice);
                            $order = $order->refreshShippingCost();
                            $order->update();
                            if (isset($new_carrier))
                                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'order_carrier` set id_carrier=' . (int)$new_carrier->id . ' WHERE id_order=' . (int)$order_carrier->id_order);
                            $assign['value_changed'] = Tools::displayPrice($value_change, $currency);
                            $assign['value_order'] = $order;
                            if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
                                $assign['is170'] = true;
                        }

                    }


                } elseif ($key_change == 'id_carrier') {
                    $new_carrier = new Carrier($value_change);
                    $order = new Order($id_order);
                    $cart = new Cart($order->id_cart);

                    if (!$cart->id)
                        $errors[] = $this->l('Cart') . ' #' . $order->id_cart . ' ' . $this->l('does not exists');
                    else {
                        if ($order->id_currency)
                            $currency = new Currency($order->id_currency);
                        else
                            $currency = Context::getContext()->currency;
                        $id_order_carrier = $order->getIdOrderCarrier();
                        $order_carrier = new OrderCarrier($id_order_carrier);
                        $id_tax_rules_group = (int)Db::getInstance()->getValue('SELECT id_tax_rules_group FROM `' . _DB_PREFIX_ . 'carrier` WHERE id_carrier=' . (int)$new_carrier->id);
                        $carrier_group = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop` WHERE id_carrier=' . (int)$new_carrier->id . ' AND id_shop=' . (int)Context::getContext()->shop->id);
                        unset($new_carrier->id);
                        $new_carrier->deleted = 1;
                        $new_carrier->shipping_method = 2;
                        $new_carrier->is_free = 0;
                        $new_carrier->shipping_handling = 0;
                        if ($new_carrier->add()) {
                            if ($id_tax_rules_group) {
                                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`(`id_carrier`, `id_tax_rules_group`, `id_shop`) values("' . (int)$new_carrier->id . '","' . (int)$id_tax_rules_group . '","' . (int)Context::getContext()->shop->id . '")');
                            } elseif ($carrier_group)
                                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`(`id_carrier`, `id_tax_rules_group`, `id_shop`) values("' . (int)$new_carrier->id . '","' . (int)$carrier_group['id_tax_rules_group'] . '","' . (int)$carrier_group['id_shop'] . '")');
                            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                                {
                                    $carrier_tax = $cart->getAverageProductsTaxRate();
                                }
                            } else {
                                $address = Address::initialize((int)$order->id_address_delivery);
                                $carrier_tax = $new_carrier->getTaxesRate($address);
                            }
                            $shipping_cost_tax_excl = (float)$order_carrier->shipping_cost_tax_incl / (1 + $carrier_tax / 100);
                            $rangPrice = new RangePrice();
                            $rangPrice->id_carrier = $new_carrier->id;
                            $rangPrice->delimiter1 = 0;
                            $rangPrice->delimiter2 = 9999999999;
                            if ($rangPrice->add()) {
                                $zones = Db::getInstance()->executeS('SELECT id_zone FROM ' . _DB_PREFIX_ . 'zone');
                                if ($zones) {
                                    foreach ($zones as $zone) {
                                        $delivery = new Delivery();
                                        $delivery->id_carrier = (int)$new_carrier->id;
                                        $delivery->id_range_price = (int)$rangPrice->id;
                                        $delivery->id_range_weight = 0;
                                        $delivery->id_zone = (int)$zone['id_zone'];
                                        $delivery->price = Tools::ps_round((float)$shipping_cost_tax_excl / $currency->conversion_rate, 2);
                                        $delivery->id_shop = NULL;
                                        $delivery->id_shop_group = NULL;
                                        $delivery->add();
                                        Db::getInstance()->execute('update `' . _DB_PREFIX_ . 'delivery` set id_shop = null, id_shop_group=null where id_delivery=' . (int)$delivery->id);
                                    }
                                }
                            }
                        }
                        $order->id_carrier = (int)$new_carrier->id;
                        $order_carrier->id_carrier = (int)$new_carrier->id;
                        $order_carrier->update();
                        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_invoice_tax` WHERE id_order_invoice=' . (int)$order_carrier->id_order_invoice);
                        $order->refreshShippingCost();
                        $assign['value_changed'] = $new_carrier->name;
                        $order->update();
                    }

                } elseif ($table == 'ets_paymentmethod_order' && $key_change == 'fee') {
                    $order = new Order($key_value);
                    $fee_old = (float)Db::getInstance()->getValue('SELECT fee FROM ' . _DB_PREFIX_ . pSQL($table) . ' WHERE id_order=' . (int)$key_value);
                    $fee_incl_old = (float)Db::getInstance()->getValue('SELECT fee_incl FROM ' . _DB_PREFIX_ . pSQL($table) . ' WHERE id_order=' . (int)$key_value);
                    $fee_new = (float)$value_change;
                    $tax = ($fee_incl_old - $fee_old) / $fee_old;
                    $fee_incl_new = Tools::ps_round($fee_new + $fee_new * $tax, 2);
                    $order->total_paid += ($fee_incl_new - $fee_incl_old);
                    $order->total_paid_tax_incl += ($fee_incl_new - $fee_incl_old);
                    $order->total_paid_tax_excl += ($fee_new - $fee_old);
                    $order->total_paid_real += ($fee_new - $fee_old);
                    $order->update();
                    if ($order->id_currency)
                        $currency = new Currency($order->id_currency);
                    else
                        $currency = Context::getContext()->currency;
                    $assign['value_changed'] = Tools::displayPrice($value_change, $currency);
                    $assign['total_paid_tax_incl'] = Tools::displayPrice($order->total_paid_tax_incl, $currency);
                    if (Db::getInstance()->getRow('SELECT fee FROM ' . _DB_PREFIX_ . pSQL($table) . ' WHERE id_order=' . (int)$key_value))
                        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . (pSQL($table)) . ' SET ' . pSQL($key_change) . ' = "' . pSQL($value_change) . '",fee_incl="' . (float)$fee_incl_new . '" WHERE ' . pSQL($primary_key) . ' = "' . pSQL($key_value) . '"');
                    else
                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_paymentmethod_order`(id_paymentmethod,id_order,method_name,fee,fee_incl) values(0,"' . (int)$order->id . '","' . pSQL($order->payment) . '","' . (float)$value_change . '","' . (float)$fee_incl_new . '")');
                } elseif ($key_change == 'tracking_number') {
                    $id_order_carrier = (int)$key_value;
                    $order_carrier = new OrderCarrier((int)$id_order_carrier);
                    $old_tracking_number = $order_carrier->tracking_number;
                    $tracking_number = $value_change;
                    $order = new Order($id_order);
                    $order->shipping_number = $tracking_number;
                    $order->update();
                    $order_carrier->tracking_number = pSQL($tracking_number);
                    $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
                    if ($order_carrier->update()) {
                        if (!empty($tracking_number) && $old_tracking_number != $tracking_number) {
                            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                                if ($order_carrier->sendInTransitEmail($order)) {
                                    $customer = new Customer((int)$order->id_customer);
                                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array(
                                        'order' => $order,
                                        'customer' => $customer,
                                        'carrier' => $carrier,
                                    ), null, false, true, false, $order->id_shop);
                                }
                            } else {
                                $customer = new Customer((int)$order->id_customer);
                                $templateVars = array(
                                    '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                                    '{firstname}' => $customer->firstname,
                                    '{lastname}' => $customer->lastname,
                                    '{id_order}' => $order->id,
                                    '{shipping_number}' => $order->shipping_number,
                                    '{order_name}' => $order->getUniqReference()
                                );
                                if (@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
                                    $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null,
                                    _PS_MAIL_DIR_, true, (int)$order->id_shop)) {
                                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier), null, false, true, false, $order->id_shop);
                                }
                            }
                        }
                    }
                    if ($carrier->url && !empty($tracking_number))
                        $assign['value_changed'] = Module::getInstanceByName('ets_ordermanager')->displayText($tracking_number, 'a', array('href' => str_replace('@', $tracking_number, $carrier->url)));
                    else
                        $assign['value_changed'] = $tracking_number;
                } else {
                    Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . (pSQL($table)) . ' SET ' . pSQL($key_change) . ' = "' . pSQL($value_change,true) . '" WHERE ' . pSQL($primary_key) . ' = "' . pSQL($key_value) . '"');
                    if ($key_change == 'firstname' || $key_change == 'lastname') {
                        $customer = new Customer($key_value);
                        $assign['value_changed'] = $key_change == 'firstname' ? Tools::substr($value_change, 0, 1) . '. ' . $customer->lastname : Tools::substr($customer->firstname, 0, 1) . '. ' . $value_change;
                    }

                }
            }

        }
    }
    public static function getNoteOrderByID($id_order)
    {
        return Db::getInstance()->getValue('SELECT order_note FROM `' . _DB_PREFIX_ . 'orders` WHERE id_order=' . (int)$id_order);
    }
    public static function getNoteOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT order_note FROM `' . _DB_PREFIX_ . 'orders` WHERE id_order=' . (int)$id_order);
    }
    public static function getProfiles()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $permistions = array('read', 'create', 'update', 'delete');
            $slug = 'ROLE_MOD_TAB_ADMINORDERS_';
            $profiles = Profile::getProfiles(Context::getContext()->language->id);
            foreach ($profiles as &$profile) {
                $all = 1;
                foreach ($permistions as $permistion) {
                    $id_authorization_role = Db::getInstance()->getValue('SELECT id_authorization_role FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug ="' . pSQL($slug . Tools::strtoupper($permistion)) . '"');
                    $profile[$permistion] = Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile ="' . (int)$profile['id_profile'] . '" AND id_authorization_role=' . (int)$id_authorization_role) ? true : false;
                    if (!$profile[$permistion])
                        $all = 0;
                }
                $profile['all'] = $all;
            }
        } else {
            $tabId = Tab::getIdFromClassName('AdminOrders');
            $permistions = array('view', 'add', 'edit', 'delete');
            $profiles = Profile::getProfiles(Context::getContext()->language->id);
            foreach ($profiles as &$profile) {
                $all = 1;
                foreach ($permistions as $permistion) {
                    $profile[$permistion] = Db::getInstance()->getValue('SELECT `' . pSQL($permistion) . '` FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile ="' . (int)$profile['id_profile'] . '" AND id_tab=' . (int)$tabId);
                    if (!$profile[$permistion])
                        $all = 0;
                }
                $profile['all'] = $all;
            }
        }
        return array(
            'permistions' => $permistions,
            'profiles' => $profiles,
            'is_ps16' => version_compare(_PS_VERSION_, '1.7', '>=') ? false : true,
        );
    }
    public static function updatePermistionProfile($perm, $id_profile,$enabled)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $slug = 'ROLE_MOD_TAB_ADMINORDERS_';
            $id_authorization_role = Db::getInstance()->getValue('SELECT id_authorization_role FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug ="' . pSQL($slug . Tools::strtoupper($perm)) . '"');
            if ($id_authorization_role) {
                if ($enabled) {
                    if (!Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile ="' . (int)$id_profile . '" AND id_authorization_role=' . (int)$id_authorization_role))
                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'access`(id_profile,id_authorization_role) VALUES("' . (int)$id_profile . '","' . (int)$id_authorization_role . '")');
                } else {
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile =' . (int)$id_profile . ' AND id_authorization_role=' . (int)$id_authorization_role);
                }
            }
        } else {
            $tabId = Tab::getIdFromClassName('AdminOrders');
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'access` SET `' . pSQL($perm) . '` = "' . (int)$enabled . '" WHERE id_profile=' . (int)$id_profile . ' AND id_tab=' . (int)$tabId);
        }
    }
    public static function checkAccess($action)
    {
        if (Context::getContext()->employee->id_profile == 1)
            return true;
        else {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $slug = 'ROLE_MOD_TAB_ADMINORDERS_';
                $id_authorization_role = Db::getInstance()->getValue('SELECT id_authorization_role FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug ="' . pSQL($slug . Tools::strtoupper($action)) . '"');
                return Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile ="' . (int)Context::getContext()->employee->id_profile . '" AND id_authorization_role=' . (int)$id_authorization_role) ? true : false;
            } else {
                $permistions = array('read' => 'view', 'create' => 'add', 'update' => 'edit', 'delete' => 'delete');
                $tabId = Tab::getIdFromClassName('AdminOrders');
                return Db::getInstance()->getValue('SELECT `' . pSQL($permistions[$action]) . '` FROM `' . _DB_PREFIX_ . 'access` WHERE id_profile=' . (int)Context::getContext()->employee->id_profile . ' AND id_tab=' . (int)$tabId);
            }
        }
    }
    public static function checkViewModule()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $slug = 'ROLE_MOD_MODULE_ETS_ORDERMANAGER_READ';
            $id_authorization_role = Db::getInstance()->getValue('SELECT id_authorization_role FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE slug ="' . pSQL($slug) . '"');
            return Db::getInstance()->getValue('SELECT id_profile FROM `' . _DB_PREFIX_ . 'module_access` WHERE id_profile ="' . (int)Context::getContext()->employee->id_profile . '" AND id_authorization_role=' . (int)$id_authorization_role) ? true : false;
        } else {
            $ets_ordermanager = Module::getInstanceByName('ets_ordermanager');
            return (int)Db::getInstance()->getValue('SELECT view FROM `' . _DB_PREFIX_ . 'module_access` WHERE id_profile="' . (int)Context::getContext()->employee->id_profile . '" AND id_module="' . (int)$ets_ordermanager->id . '"');
        }
    }
    public static function getListCarriersByIDOrder($id_order)
    {
        $order = new Order($id_order);
        $carriers = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'carrier` c
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl on (c.id_carrier = cl.id_carrier AND cl.id_lang="' . (int)Context::getContext()->language->id . '")
        WHERE ((c.deleted!=1 AND c.active=1) OR c.id_carrier = "' . (int)$order->id_carrier . '") GROUP BY c.id_carrier');
        if ($carriers) {
            foreach ($carriers as &$carrier) {
                if (!$carrier['name'])
                    $carrier['name'] = Context::getContext()->shop->name;
            }
        }
        return $carriers;
    }
    public static function getProductsDetail($id_order)
    {
        $order = new Order($id_order);
        $products = $order->getProductsDetail();
        if ($products) {
            foreach ($products as &$product) {
                $product_class = new Product($product['product_id'], false, Context::getContext()->language->id);
                $image = false;
                if ($product['product_attribute_id']) {
                    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                    INNER JOIN `' . _DB_PREFIX_ . 'image` i ON pai.id_image=i.id_image WHERE pai.id_product_attribute=' . (int)$product['product_attribute_id'];
                    if (!$image = Db::getInstance()->getRow($sql . ' AND i.cover=1'))
                        $image = Db::getInstance()->getRow($sql);
                }
                if (!$image) {
                    $sql = 'SELECT i.id_image FROM `' . _DB_PREFIX_ . 'image` i';
                    if ($product['product_attribute_id'])
                        $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON (i.id_image=pai.id_image AND pai.id_product_attribute="' . (int)$product['product_attribute_id'] . '")';
                    $sql .= ' WHERE i.id_product="' . (int)$product['product_id'] . '"';
                    if (!$image = Db::getInstance()->getRow($sql . ' AND i.cover=1')) {
                        $image = Db::getInstance()->getRow($sql);
                    }
                }

                if ($image) {
                    if (version_compare(_PS_VERSION_, '1.7', '>='))
                        $type_image = ImageType::getFormattedName('small');
                    else
                        $type_image = Ets_ordermanager::getFormatedName('small');
                    $product['image'] = Context::getContext()->link->getImageLink($product_class->link_rewrite, $image['id_image'], $type_image);
                } else {
                    $product['image'] = '';
                }
            }
        }
        return $products;
    }
    public static function getCarrierOrder($id_carrier, $id_order)
    {
        $order = new Order($id_order);
        $order_carrier =  Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'order_carrier` WHERE id_carrier = "' . (int)$id_carrier . '" AND id_order="' . (int)$id_order . '"');
        $order_carrier['tax_incl'] = $order->getTaxCalculationMethod() == PS_TAX_INC ? true : false;
        $order_carrier['weight_unit'] = Configuration::get('PS_WEIGHT_UNIT');
        $currency = new Currency($order->id_currency);
        $order_carrier['sign'] = $currency->sign;
        return $order_carrier;
    }

    /**
     * @param Order $order
     * @throws PrestaShopDatabaseException
     */
    public static function actionObjectOrderDeleteAfter($order)
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_invoice` where id_order=' . (int)$order->id);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_slip` where id_order=' . (int)$order->id);
        $order_details = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'order_detail` where id_order=' . (int)$order->id);
        foreach ($order_details as $order_detail) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_detail_tax` WHERE id_order_detail=' . (int)$order_detail['id_order_detail']);
        }
        self::refundQuantityProduct($order->id);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_detail` where id_order=' . (int)$order->id);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_carrier` WHERE id_order=' . (int)$order->id);
    }
    public static function refundQuantityProduct($id_order)
    {
        $order_details = Db::getInstance()->executeS('SELECT od.* FROM `' . _DB_PREFIX_ . 'order_detail` od
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_odm_order_detail_refund` odr ON (od.id_order_detail = odr.id_order_detail)
        where id_order=' . (int)$id_order . ' AND odr.id_order_detail is null');
        foreach ($order_details as $order_detail) {
            $quantity = $order_detail['product_quantity'] + StockAvailable::getQuantityAvailableByProduct($order_detail['product_id'], $order_detail['product_attribute_id'], $order_detail['id_shop']);
            StockAvailable::setQuantity($order_detail['product_id'], (int)$order_detail['product_attribute_id'], $quantity, $order_detail['id_shop']);
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_odm_order_detail_refund`(id_order_detail,refund) VALUES("' . (int)$order_detail['id_order_detail'] . '","1")');
        }
    }
    public static function upQuantityProduct($id_order)
    {
        $order_details = Db::getInstance()->executeS('SELECT od.* FROM `' . _DB_PREFIX_ . 'order_detail` od
        INNER JOIN `' . _DB_PREFIX_ . 'ets_odm_order_detail_refund` odr ON (od.id_order_detail = odr.id_order_detail)
        where id_order=' . (int)$id_order);
        foreach ($order_details as $order_detail) {
            $quantity = StockAvailable::getQuantityAvailableByProduct($order_detail['product_id'], $order_detail['product_attribute_id'], $order_detail['id_shop']) - $order_detail['product_quantity'];
            StockAvailable::setQuantity($order_detail['product_id'], (int)$order_detail['product_attribute_id'], $quantity > 0 ? $quantity : 0, $order_detail['id_shop']);
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_odm_order_detail_refund` WHERE id_order_detail = ' . (int)$order_detail['id_order_detail']);
        }
    }
    public static function getAdminFilter()
    {
        return Db::getInstance()->getValue('SELECT filter FROM `' . _DB_PREFIX_ . 'admin_filter` WHERE employee="' . (int)Context::getContext()->employee->id . '" AND filter_id="order"');
    }
    public static function getCustomerByIDs($ids)
    {
        if($ids && is_array($ids))
            return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'customer` WHERE id_customer IN (' . implode(',', array_map('intval', $ids)) . ')');
    }
    public static function getCustomerByQuery($query)
    {
        $sql = 'SELECT c.`id_customer`, c.`firstname`, c.lastname,c.email
        		FROM `' . _DB_PREFIX_ . 'customer` c
        		WHERE (c.lastname LIKE \'%' . pSQL($query) . '%\' OR c.firstname LIKE \'%' . pSQL($query) . '%\' OR c.email LIKE \'%' . pSQL($query) . '%\')';
        return Db::getInstance()->executeS($sql);
    }
    public static function resetFilter()
    {
        $filter = self::getAdminFilter();
        if ($filter) {
            $admin_filter = json_decode($filter, true);
            if (isset($admin_filter['filters']) && count($admin_filter['filters']) >= 2) {
                $admin_filter['filters'] = array();
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'admin_filter` set filter="' . pSQL(json_encode($admin_filter)) . '" WHERE filter_id="order" AND employee="' . (int)Context::getContext()->employee->id . '"');
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders'));
            }
        }
    }
    public static function isOrderDeleted($id_order)
    {
        return (int)Db::getInstance()->getValue('SELECT deleted FROM `' . _DB_PREFIX_ . 'orders` WHERE id_order=' . (int)$id_order);
    }
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        $query = new DbQuery();
        $query->select('`id_' . bqSQL($table) . '`');
        $query->from($table);
        if ($has_active_column) {
            $query->where('`active` = 1');
        }

        return (bool)Db::getInstance()->getValue($query);
    }
    public static function getCountriesHasOrder()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `' . _DB_PREFIX_ . 'orders` o
			' . Shop::addSqlAssociation('orders', 'o') . '
			INNER JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `' . _DB_PREFIX_ . 'country` c ON a.id_country = c.id_country
			INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . ')
			ORDER BY cl.name ASC');
    }
    public static function getMessageReply($id_customer_thread,$id_customer_message)
    {
        return (int)Db::getInstance()->getValue('SELECT id_customer_message FROM `' . _DB_PREFIX_ . 'customer_message` WHERE id_customer_thread = "' . (int)$id_customer_thread . '" AND id_employee=1 AND id_customer_message > "' . (int)$id_customer_message . '"');
    }
    public static function checkCreatedColumn($table, $column)
    {
        $fieldsCustomers = Db::getInstance()->ExecuteS('DESCRIBE ' . _DB_PREFIX_ . pSQL($table));
        $check_add = false;
        foreach ($fieldsCustomers as $field) {
            if ($field['Field'] == $column) {
                $check_add = true;
                break;
            }
        }
        return $check_add;
    }
}