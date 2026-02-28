<?php
/**
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }


class EtsAbancartShoppingCart extends ObjectModel
{
    public $id_cart;
    public $id_customer;
    public $cart_name;
    public $date_add;
    public static $definition = array(
        'table' => 'ets_abancart_shopping_cart',
        'primary' => 'id_cart',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'cart_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function itemExist($id_cart)
    {
        return (int)Db::getInstance()->getValue('SELECT id_cart FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'` WHERE id_cart ='.(int)$id_cart);
    }

    public static function getDataShoppingCart($id_customer, $id_lang, $id_shop=null)
    {
        if(!$id_shop){
            $id_shop = Context::getContext()->shop->id;
        }
        return Db::getInstance()->executeS('
            SELECT sc.id_cart, sc.cart_name, cart.date_upd FROM `' . _DB_PREFIX_ . 'ets_abancart_shopping_cart` sc
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` cart ON (cart.id_cart = sc.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = cart.id_cart)
            WHERE o.id_order is NULL AND cart.id_cart is NOT NULL 
                AND cart.id_shop = ' . (int)$id_shop . ' 
                AND cart.id_lang = ' . (int)$id_lang . '
                AND sc.id_customer = ' . (int)$id_customer . '
        ');
    }

    public static function getIdByCartId($id_cart)
    {
        if (method_exists('Order', 'getIdByCartId'))
            return Order::getIdByCartId($id_cart);
        if ($id_cart < 1) {
            return false;
        }

        $sql = 'SELECT `id_order`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_cart` = ' . (int) $id_cart .
            Shop::addSqlRestriction();

        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? (int) $result : false;
    }

    public static function makeNewCart(Cart $cloneCart,Context $context = null)
    {
        if (!Validate::isLoadedObject($cloneCart)) {
            return false;
        }
        if ($context == null) {
            $context = Context::getContext();
        }

        $cart = new Cart($cloneCart->id);
        $cart->id = null;
        $cart->id_shop = $context->shop->id_shop;
        $cart->id_shop_group = $context->shop->id_shop_group;
        $cart->id_customer = $context->customer->id;

        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_delivery)) {
            $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_invoice)) {
            $cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }

        if ($cart->id_customer) {
            $cart->secure_key = $context->customer->secure_key;
        }

        $cart->add();

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $cloneCart->id);

        $orderId = self::getIdByCartId((int) $cloneCart->id);
        $product_gift = [];
        if ($orderId) {
            $product_gift = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT cr.`gift_product`, cr.`gift_product_attribute` FROM `' . _DB_PREFIX_ . 'cart_rule` cr LEFT JOIN `' . _DB_PREFIX_ . 'order_cart_rule` ocr ON (ocr.`id_order` = ' . (int) $orderId . ') WHERE ocr.`deleted` = 0 AND ocr.`id_cart_rule` = cr.`id_cart_rule`');
        }

        $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $cart->id_address_delivery : 0;

        // Customized products: duplicate customizations before products so that we get new id_customizations
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'customization` c
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = ' . (int) $cloneCart->id
        );

        // Get datas from customization table
        $customs_by_id = [];
        foreach ($customs as $custom) {
            if (!isset($customs_by_id[$custom['id_customization']])) {
                $customs_by_id[$custom['id_customization']] = [
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                    'quantity' => $custom['quantity'],
                ];
            }
        }

        // Backward compatibility: if true set customizations quantity to 0, they will be updated in Cart::_updateCustomizationQuantity
        $new_customization_method = (int) Db::getInstance()->getValue(
                '
            SELECT COUNT(`id_customization`) FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_cart` = ' . (int) $cloneCart->id .
                ' AND `id_customization` != 0'
            ) > 0;

        // Insert new customizations
        $custom_ids = [];
        foreach ($customs_by_id as $customization_id => $val) {
            if ($new_customization_method) {
                $val['quantity'] = 0;
            }
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (id_cart, id_product_attribute, id_product, `id_address_delivery`, quantity, `quantity_refunded`, `quantity_returned`, `in_cart`)
                VALUES(' . (int) $cart->id . ', ' . (int) $val['id_product_attribute'] . ', ' . (int) $val['id_product'] . ', ' . (int) $id_address_delivery . ', ' . (int) $val['quantity'] . ', 0, 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        // Insert customized_data
        if (count($customs)) {
            $first = true;
            $sql_custom_data = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`, `id_module`, `price`, `weight`) VALUES ';
            foreach ($customs as $custom) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }

                $customized_value = $custom['value'];

                if ((int) $custom['type'] == Product::CUSTOMIZE_FILE) {
                    $customized_value = md5(uniqid(mt_rand(0, mt_getrandmax()), true));
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'], _PS_UPLOAD_DIR_ . $customized_value);
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'] . '_small', _PS_UPLOAD_DIR_ . $customized_value . '_small');
                }

                $sql_custom_data .= '(' . (int) $custom_ids[$custom['id_customization']] . ', ' . (int) $custom['type'] . ', ' .
                    (int) $custom['index'] . ', \'' . pSQL($customized_value) . '\', ' .
                    (int) $custom['id_module'] . ', ' . (float) $custom['price'] . ', ' . (float) $custom['weight'] . ')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        foreach ($products as $product) {
            if ($id_address_delivery) {
                if (Customer::customerHasAddress((int) $cart->id_customer, $product['id_address_delivery'])) {
                    $id_address_delivery = $product['id_address_delivery'];
                }
            }

            foreach ($product_gift as $gift) {
                if (isset($gift['gift_product'], $gift['gift_product_attribute']) && (int) $gift['gift_product'] == (int) $product['id_product'] && (int) $gift['gift_product_attribute'] == (int) $product['id_product_attribute']) {
                    $product['quantity'] = (int) $product['quantity'] - 1;
                }
            }

            $id_customization = (int) $product['id_customization'];

            $success &= $cart->updateQty(
                (int) $product['quantity'],
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                isset($custom_ids[$id_customization]) ? (int) $custom_ids[$id_customization] : 0,
                'up',
                (int) $id_address_delivery,
                new Shop((int) $cart->id_shop),
                false,
                false
            );
        }

        return ['cart' => $cart, 'success' => $success];
    }
}