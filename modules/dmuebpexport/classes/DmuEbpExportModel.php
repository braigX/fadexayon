<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2024 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DmuEbpExportModel extends ObjectModel
{
    public static function getSlipsIdByDate($dateFrom, $dateTo)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_order_slip`
		FROM `' . _DB_PREFIX_ . 'order_slip` os
		INNER JOIN `' . _DB_PREFIX_ . 'orders` o
            ON o.`id_order` = os.`id_order`
		WHERE os.`date_add` BETWEEN \'' . pSQL($dateFrom) . ' 00:00:00\' AND \'' . pSQL($dateTo) . ' 23:59:59\'
		' . Shop::addSqlRestriction() . '
		ORDER BY os.`date_add` ASC');

        $slips = [];
        foreach ($result as $slip) {
            $slips[] = (int) $slip['id_order_slip'];
        }

        return $slips;
    }

    public static function getOrdersIdInvoiceByDate($date_from, $date_to, $id_customer = null, $type = null)
    {
        $sql = 'SELECT `id_order`
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \'' . pSQL($date_to) . '\' 
                    AND invoice_date >= \'' . pSQL($date_from) . '\'
                    ' . Shop::addSqlRestriction()
                . ($type ? ' AND ' . pSQL((string) $type) . '_number != 0' : '')
                . ($id_customer ? ' AND id_customer = ' . (int) $id_customer : '') .
                ' ORDER BY invoice_date ASC';
        /*
        $sql = 'SELECT o.`id_order`
                FROM `'._DB_PREFIX_.'orders` o
                INNER JOIN `'._DB_PREFIX_.'order_invoice` oi
                    ON oi.id_order = o.id_order
                WHERE DATE_ADD(oi.date_add, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
                    AND oi.date_add >= \''.pSQL($date_from).'\'
                    AND o.valid = 1
                    '.Shop::addSqlRestriction()
                .($type ? ' AND o.'.pSQL((string)$type).'_number != 0' : '')
                .($id_customer ? ' AND o.id_customer = '.(int)$id_customer : '').
                ' ORDER BY o.invoice_date ASC';
        */
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = [];
        foreach ($result as $order) {
            $orders[] = (int) $order['id_order'];
        }

        return $orders;
    }

    public static function encodeToCSV($string, $strict = true)
    {
        $removeacc = Configuration::get('DMUEBP_REMOVEACC');
        $removespe = Configuration::get('DMUEBP_REMOVESPE');

        // 26/11/2018 : Retrait de tout les caractères spéciaux
        if (true == $strict) {
            if (1 == (int) $removeacc) {
                $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
            }

            if (1 == (int) $removespe) {
                $string = str_replace('%', ' pourc', $string);
                $string = preg_replace('`([/,\?;\.:\/!\*\$\^&~\'"#\{\(\[\-|_@\)\]\}]+)`iUs', ' ', $string);
            }

            $string = Tools::strtolower($string);
        }

        $string = str_replace(';', ' ', $string);
        $string = str_replace("\r", ' ', $string);
        $string = str_replace("\n", ' ', $string);
        $string = preg_replace('# +#', ' ', $string);
        $string = trim($string);

        return $string;
    }

    public static function alterTables($method)
    {
        if ('add' == $method) {
            try {
                $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product 
                ADD (`accounting_no_vat` VARCHAR(64) NULL, `accounting_vat` VARCHAR(64) NULL)';
                Db::getInstance()->Execute($sql);
            } catch (Exception $e) {
            }
        }

        if ('remove' == $method) {
            try {
                $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product DROP COLUMN `accounting_no_vat`';
                Db::getInstance()->Execute($sql);

                $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product DROP COLUMN `accounting_vat`';
                Db::getInstance()->Execute($sql);
            } catch (Exception $e) {
            }
        }

        return true;
    }

    public static function saveProductAccountingInfos($id_product, $accounting_no_vat, $accounting_vat)
    {
        Db::getInstance()->Execute('
            UPDATE `' . _DB_PREFIX_ . 'product`
            SET accounting_no_vat = "' . pSQL($accounting_no_vat) . '", accounting_vat = "' . pSQL($accounting_vat) . '"
            WHERE id_product = ' . (int) $id_product);
    }

    public static function getProductAccountingInfos($id_product)
    {
        $sql = '
        SELECT accounting_no_vat, accounting_vat 
        FROM `' . _DB_PREFIX_ . 'product` 
        WHERE id_product = ' . (int) $id_product;
        if ($accounting_infos = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return $accounting_infos;
        }

        return [['accounting_no_vat' => '', 'accounting_vat' => '']];
    }

    public static function getPaymentModeSimpleName($name)
    {
        /*
        Exemple de valeurs :
        - Compte eBay PayPal 1902
        - Compte eBay PersonalCheck 3236
        - Compte eBay MoneyXferAccepted 3433
        - Compte eBay MOCC 4124
        - Compte EBAY.FR - 111823255744-1447714426001
        - Compte FNAC.COM - 0MVLOLOLEAKAC
        - Compte CDISCOUNT.COM - 1510211759PLLX4
        - Compte PRICEMINISTER.FR - 216393687
        - Compte DARTY.FR - 72264391_466383-A
        */
        $mode_payment_arr = [
            'eBay PayPal',
            'eBay PersonalCheck',
            'eBay MoneyXferAccepted',
            'eBay MOCC',
            'eBay',
            'Fnac',
            'Cdiscount',
            'Priceminister',
            'Amazon',
            'Darty',
        ];

        foreach ($mode_payment_arr as $value) {
            if (false !== strpos(Tools::strtoupper($name), Tools::strtoupper($value))) {
                return $value;
            }
        }

        return $name;
    }

    public static function getDefaultTaxRuleTax($id_tax_rules_group)
    {
        $id_default_country = Configuration::get('PS_COUNTRY_DEFAULT');
        $sql = 'SELECT id_tax FROM `' . _DB_PREFIX_ . 'tax_rule` WHERE id_tax_rules_group = ' . (int) $id_tax_rules_group . '
                AND id_country = ' . (int) $id_default_country;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getModifiedTaxRuleCountry($id_tax_rules_group)
    {
        $id_tax_default = self::getDefaultTaxRuleTax($id_tax_rules_group);
        $sql = 'SELECT tr.*, t.rate, cl.name AS country_name FROM `' . _DB_PREFIX_ . 'tax_rule` tr
                INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON tr.id_tax = t.id_tax
                INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON tr.id_country = cl.id_country
                AND cl.id_lang = ' . (int) Context::getContext()->language->id . '
                WHERE tr.id_tax_rules_group = ' . (int) $id_tax_rules_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getTaxRulesGroupAssociation($id_current_tax_rule_group)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'dmuebp_taxrule
                WHERE new_tax_rules_group = ' . (int) $id_current_tax_rule_group;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function setTaxRulesGroupAssociation($old_tax_rules_group, $new_tax_rules_group)
    {
        $sql = 'REPLACE INTO ' . _DB_PREFIX_ . 'dmuebp_taxrule (new_tax_rules_group, old_tax_rules_group)
                VALUES (' . (int) $new_tax_rules_group . ', ' . (int) $old_tax_rules_group . ')';
        Db::getInstance()->Execute($sql);

        self::updateConfigTaxRules($old_tax_rules_group, $new_tax_rules_group);
    }

    public static function updateConfigTaxRules($old_tax_rules_group, $new_tax_rules_group)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'configuration WHERE name LIKE "DMUEBP_COMPTE_TVA_' . (int) $old_tax_rules_group . '%"';
        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        foreach ($configs as $config) {
            $new_name = str_replace('DMUEBP_COMPTE_TVA_' . (int) $old_tax_rules_group, 'DMUEBP_COMPTE_TVA_' . (int) $new_tax_rules_group, $config['name']);
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'configuration SET name="' . $new_name . '" WHERE id_configuration=' . (int) $config['id_configuration'];
            Db::getInstance()->Execute($sql);
        }

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'configuration WHERE name LIKE "DMUEBP_COMPTE_PRODUITS_TVA_' . (int) $old_tax_rules_group . '%"';
        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        foreach ($configs as $config) {
            $new_name = str_replace('DMUEBP_COMPTE_PRODUITS_TVA_' . (int) $old_tax_rules_group, 'DMUEBP_COMPTE_PRODUITS_TVA_' . (int) $new_tax_rules_group, $config['name']);
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'configuration SET name="' . $new_name . '" WHERE id_configuration=' . (int) $config['id_configuration'];
            Db::getInstance()->Execute($sql);
        }
    }
}
