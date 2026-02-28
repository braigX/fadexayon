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

if (!defined('_PS_VERSION_')) {
    exit;
}

class EtsRVCartRule extends EtsRVCore
{
    static $_INSTANCE;

    public static function getInstance()
    {
        if (!self::$_INSTANCE)
            self::$_INSTANCE = new self();
        return self::$_INSTANCE;
    }

    public function addCartRule($id_customer, $date_from = null)
    {
        if (!Validate::isUnsignedInt($id_customer)) {
            return false;
        }
        $cart_rule = new CartRule();
        $cart_rule->id_customer = (int)$id_customer;
        if ($language = Language::getLanguages(false)) {
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            foreach ($language as $l)
                $cart_rule->name[$l['id_lang']] = Configuration::get('ETS_RV_DISCOUNT_NAME', (int)$l['id_lang']) ?: Configuration::get('ETS_RV_DISCOUNT_NAME', (int)$id_lang_default);
        }
        $cart_rule->free_shipping = (int)Configuration::get('ETS_RV_FREE_SHIPPING');

        do {
            $cart_rule->code = (trim(Configuration::get('ETS_RV_DISCOUNT_PREFIX')) ?: '') . Tools::strtoupper(Tools::passwdGen(10));
        } while ((int)Db::getInstance()->getValue('SELECT `id_cart_rule` FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `code`=\'' . pSQL($cart_rule->code) . '\'') > 0);

        $apply_discount = trim(Configuration::get('ETS_RV_APPLY_DISCOUNT'));
        if ($apply_discount !== 'percent') {
            $cart_rule->reduction_currency = (int)Configuration::get('ETS_RV_ID_CURRENCY');
            $cart_rule->reduction_tax = (int)Configuration::get('ETS_RV_REDUCTION_TAX');
            $cart_rule->reduction_amount = (float)Configuration::get('ETS_RV_REDUCTION_AMOUNT');
        } else {
            $cart_rule->reduction_percent = ($percent = Configuration::get('ETS_RV_REDUCTION_PERCENT')) && Validate::isPercentage($percent) ? $percent : 0;
            $cart_rule->reduction_exclude_special = (int)Configuration::get('ETS_RV_REDUCTION_EXCLUDE_SPECIAL');
        }
        $cart_rule->minimum_amount = Configuration::get('ETS_RV_MINIMUM_AMOUNT');
        $cart_rule->minimum_amount_currency = (int)Configuration::get('ETS_RV_MINIMUM_AMOUNT_CURRENCY');
        $cart_rule->minimum_amount_shipping = (int)Configuration::get('ETS_RV_MINIMUM_AMOUNT_SHIPPING');
        $cart_rule->minimum_amount_tax = (int)Configuration::get('ETS_RV_MINIMUM_AMOUNT_TAX');
        $cart_rule->date_from = ($date_from !== null ? $date_from : date('Y-m-d H:i:s'));
        $cart_rule->date_to = ($days = (int)Configuration::get('ETS_RV_APPLY_DISCOUNT_IN')) ? date('Y-m-d H:i:s', strtotime($cart_rule->date_from . ' +' . $days . ' days')) : date('Y-m-d H:i:s', strtotime($cart_rule->date_from . ' +30 days'));
        $cart_rule->highlight = (int)Configuration::get('ETS_RV_DISCOUNT_HIGHLIGHT') > 0 ? 1 : 0;
        if ($cart_rule->validateFields(false) && !$cart_rule->add(true, true)) {
            return false;
        }
        return $cart_rule;
    }

    public function addLogCartRule($id_cart_rule, $id_product, $id_customer)
    {
        if (!Validate::isUnsignedInt($id_cart_rule) ||
            !Validate::isUnsignedInt($id_product) ||
            !Validate::isUnsignedInt($id_customer) ||
            Db::getInstance()->getValue('SELECT id_cart_rule FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` WHERE id_cart_rule=' . (int)$id_cart_rule)
        ) {
            return false;
        }

        return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` (id_cart_rule, id_product, id_customer) VALUES(' . (int)$id_cart_rule . ', ' . (int)$id_product . ', ' . (int)$id_customer . ')');
    }

    public function generalCode($id_customer, $id_product, $rating = 3, $back_office = false)
    {
        if (!$back_office && (int)Configuration::get('ETS_RV_MODERATE') && !(int)Configuration::get('ETS_RV_PURCHASED_PRODUCT_APPROVE')
            || !$back_office && (int)Configuration::get('ETS_RV_PURCHASED_PRODUCT_APPROVE') && !EtsRVProductComment::isPurchased($id_customer, $id_product)
            || !(int)Configuration::get('ETS_RV_DISCOUNT_ENABLED')
            || ((int)Configuration::get('ETS_RV_DISCOUNT_ONLY_CUSTOMER') && $this->cartRuleExist($id_customer, $id_product))
            || (int)Configuration::get('ETS_RV_DISCOUNT_HIGH_RATING') && (int)$rating < 5
        ) {
            return false;
        }
        $discount_option = Configuration::get('ETS_RV_DISCOUNT_OPTION');

        if ($discount_option == 'auto') {
            $cart_rule = $this->addCartRule($id_customer);
            if ($cart_rule !== false && $cart_rule->id) {
                self::addLogCartRule($cart_rule->id, $id_product, $id_customer);

                return $cart_rule;
            }
        } elseif ($discount_option != 'no') {
            $code = Configuration::get('ETS_RV_DISCOUNT_CODE');
            $cart_rule = new CartRule((int)CartRule::getIdByCode($code));
            if ($cart_rule->id) {
                self::addLogCartRule($cart_rule->id, $id_product, $id_customer);

                return $cart_rule;
            }
        }

        return false;
    }

    public function cartRuleExist($id_customer, $id_product = 0)
    {
        if (!Validate::isUnsignedInt($id_customer) ||
            !Validate::isUnsignedInt($id_product)) {
            return false;
        }
        return (int)Db::getInstance()->getValue('SELECT ccr.id_cart_rule 
            FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` ccr 
            INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (ccr.id_cart_rule = cr.id_cart_rule) 
            WHERE cr.id_customer=' . (int)$id_customer . ($id_product ? ' AND ccr.id_product=' . (int)$id_product : '')
        );
    }

    public function doShortCode($id_product, $rating, Customer $customer, CartRule &$cart_rule = null, $back_office = false)
    {
        if (!$customer->id ||
            !$id_product ||
            !Validate::isUnsignedInt($id_product) ||
            !Validate::isUnsignedInt($rating) ||
            !($cart_rule = $this->generalCode($customer->id, $id_product, $rating, $back_office))
        ) {
            return false;
        }

        $discount_message = Configuration::get('ETS_RV_DISCOUNT_MESSAGE', (int)$customer->id_lang);
        $apply_discount = trim(Configuration::get('ETS_RV_APPLY_DISCOUNT'));

        return str_replace(
            [
                '[discount_value]',
                '[date_from]',
                '[date_to]',
                '[discount_code]'
            ],
            [
                $apply_discount !== 'percent' ? Tools::displayPrice(Tools::convertPriceFull($cart_rule->reduction_amount, new Currency($cart_rule->reduction_currency), $this->context->currency)) . ' ' . ($cart_rule->reduction_tax !== 0 ? $this->l('(incl. tax)', 'EtsRVCartRule') : $this->l('(excl. tax)', 'EtsRVCartRule')) : $cart_rule->reduction_percent . '%',
                $cart_rule->date_from,
                $cart_rule->date_to,
                $cart_rule->code
            ]
            ,
            str_replace([
                '[discount_code]'
            ], [
                $this->display('do-short-code.tpl', ['option' => ['voucher_box' => true, 'title' => $this->l('Click to copy', 'EtsRVCartRule')]])
            ], $discount_message)
        );
    }

    /* @var CartRule $cart_rule */
    public function getDateToString($cart_rule)
    {
        if (!$cart_rule instanceof CartRule || !Validate::isLoadedObject($cart_rule))
            return '';
        if ($cart_rule->date_to !== '') {
            $time_expired = strtotime($cart_rule->date_to) - time();
            if ($time_expired >= 86400) {
                return Tools::floorf($time_expired / 86400) . ' ' . $this->l('day(s)', 'EtsRVCartRule');
            } elseif ($time_expired >= 3600) {
                return Tools::floorf($time_expired / 3600) . ' ' . $this->l('hour(s)', 'EtsRVCartRule');
            } else
                return Tools::floorf($time_expired / 60) . ' ' . $this->l('minute(s)', 'EtsRVCartRule');
        }
        return '';
    }

    public static function getCartRuleByPromotion($discount_code)
    {
        if (EtsRVTools::tableExist('ets_pr_rule')) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_pr_rule` r
            INNER JOIN `' . _DB_PREFIX_ . 'ets_pr_action_rule` ar ON (r.id_ets_pr_rule = ar.id_ets_pr_rule)
            WHERE r.active=1 AND ar.code="' . pSQL($discount_code) . '"';
            return Db::getInstance()->getRow($sql);
        }

        return false;
    }

    public static function canUseCartRule($id_cart, $id_cart_rule, &$voucherCode)
    {
        $hasOtherCartRule = false;
        if ((int)Db::getInstance()->getValue('
                SELECT cr.id_cart_rule 
                FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_cart_rule` cr 
                WHERE cr.id_cart_rule=' . (int)$id_cart_rule
        )) {
            $id_other_cart_rule = (int)Db::getInstance()->getValue("SELECT ccr.id_cart_rule FROM `" . _DB_PREFIX_ . "cart_cart_rule` ccr WHERE id_cart=" . (int)$id_cart . " AND id_cart_rule !=" . (int)$id_cart_rule);
            if ($id_other_cart_rule)
                $hasOtherCartRule = true;
        } elseif ($id_other_cart_rule = (int)Db::getInstance()->getValue("
            SELECT ccr.id_cart_rule 
            FROM `" . _DB_PREFIX_ . "cart_cart_rule` ccr 
            WHERE id_cart=" . (int)$id_cart . " 
            AND id_cart_rule IN (
                SELECT t.id_cart_rule 
                FROM `" . _DB_PREFIX_ . "ets_rv_product_comment_cart_rule` t 
                JOIN `" . _DB_PREFIX_ . "cart_rule` c ON t.id_cart_rule=c.id_cart_rule
            )
        ")) {
            $hasOtherCartRule = true;
        }

        if ($hasOtherCartRule) {
            $cartRule = new CartRule($id_other_cart_rule);
            $voucherCode = $cartRule->code;

            return false;
        }

        return true;
    }

    public static function clearDiscountIsExpired()
    {
        $query = '
            SELECT cr.id_cart_rule 
            FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr ON (ccr.id_cart_rule = cr.id_cart_rule)
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON (c.id_cart = ccr.id_cart)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = c.id_cart)
            WHERE cr.date_to < \'' . pSQL(date('Y-m-d H:i:s')) . '\' AND o.id_cart is NULL
        ';
        $cart_rules = Db::getInstance()->executeS($query);
        $totalDeleted = 0;
        foreach ($cart_rules as $item) {
            $cart_rule = new CartRule($item['id_cart_rule']);
            if ($cart_rule->id && $cart_rule->delete()) {
                ++$totalDeleted;
            }
        }

        return $totalDeleted;
    }

    public static function deleteAll()
    {
        return Db::getInstance()->delete('ets_rv_product_comment_cart_rule', '', false);
    }
}