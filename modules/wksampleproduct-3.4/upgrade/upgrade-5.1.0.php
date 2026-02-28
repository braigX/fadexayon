<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_1_0($module)
{
    $wkQueries = [
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_product_shop` (
            `id_sample_product` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `id_product_attribute` int(10) unsigned NOT NULL,
            `max_cart_qty` int(10) unsigned NOT NULL,
            `price_type` int(10) unsigned NOT NULL,
            `price_tax` int(10) unsigned NOT NULL,
            `amount` decimal(17,2) unsigned NOT NULL,
            `price` decimal(17,2) NOT NULL,
            `button_label` varchar(32) NOT NULL,
            `description` TEXT,
            `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY  (`id_sample_product`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_sample_cart_shop` (
            `id_sample_cart` int(10) unsigned NOT NULL,
            `id_product_attribute` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_cart` int(10) unsigned NOT NULL,
            `id_order` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `id_specific_price` int(10) unsigned NOT NULL,
            `sample` tinyint(1) unsigned NOT NULL DEFAULT '1',
        PRIMARY KEY  (`id_sample_cart`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_sample_cart` ADD COLUMN `id_product_attribute` int(10) unsigned NOT NULL DEFAULT 0',
    ];

    $dbInstance = Db::getInstance();
    $success = true;
    foreach ($wkQueries as $query) {
        $success &= $dbInstance->execute(trim($query));
    }

    if ($success) {
        $shopIds = Shop::getContextListShopID();
        $dbInstance = Db::getInstance();

        $wk_sample_products = $dbInstance->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'wk_sample_product');
        if ($wk_sample_products) {
            foreach ($wk_sample_products as $wk_sample_product) {
                foreach ($shopIds as $idShop) {
                    $wkSql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_sample_product_shop`
                        (`id_sample_product`, `id_shop`, `id_product`, `id_product_attribute`, `max_cart_qty`, `price_type`, `price_tax`, `amount`, `price`, `button_label`, `description`, `active`)
                        VALUES (
                            ' . (int) $wk_sample_product['id_sample_product'] . ',
                            ' . (int) $idShop . ',
                            ' . (int) $wk_sample_product['id_product'] . ',
                            ' . (int) $wk_sample_product['id_product_attribute'] . ',
                            ' . (int) $wk_sample_product['max_cart_qty'] . ',
                            ' . (int) $wk_sample_product['price_type'] . ',
                            ' . (float) $wk_sample_product['price_tax'] . ',
                            ' . (float) $wk_sample_product['amount'] . ',
                            ' . (float) $wk_sample_product['price'] . ',
                            "' . pSQL($wk_sample_product['button_label']) . '",
                            "' . pSQL($wk_sample_product['description']) . '",
                            ' . (int) $wk_sample_product['active'] . '
                        )';

                    $dbInstance->execute($wkSql);
                }
            }
        }

        // Insert into wk_sample_cart_shop
        $wk_sample_carts = $dbInstance->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'wk_sample_cart');
        if ($wk_sample_carts) {
            foreach ($wk_sample_carts as $wk_sample_cart) {
                foreach ($shopIds as $idShop) {
                    $wkSql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_sample_cart_shop`
                        (`id_sample_cart`, `id_product_attribute`, `id_shop`, `id_cart`, `id_order`, `id_product`, `id_specific_price`, `sample`)
                        VALUES (
                            ' . (int) $wk_sample_cart['id_sample_cart'] . ',
                            ' . (int) $wk_sample_cart['id_product_attribute'] . ',
                            ' . (int) $idShop . ',
                            ' . (int) $wk_sample_cart['id_cart'] . ',
                            ' . (int) $wk_sample_cart['id_order'] . ',
                            ' . (int) $wk_sample_cart['id_product'] . ',
                            ' . (int) $wk_sample_cart['id_specific_price'] . ',
                            ' . (int) $wk_sample_cart['sample'] . '
                        )';

                    $dbInstance->execute($wkSql);
                }
            }
        }
    }

    return true;
}
