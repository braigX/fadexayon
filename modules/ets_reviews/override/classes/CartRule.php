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

class CartRule extends CartRuleCore
{
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true, $useOrderPrices = false)
    {
        $error = parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier, $useOrderPrices);

        if (Module::isEnabled('etsdiscountcombinations')) {
            if (($display_error && !$error) || (!$display_error && $error))
                $error = Module::getInstanceByName('etsdiscountcombinations')->checkValidity($this, $display_error);
        }

        /*begin-ets_reviews*/
        if (Module::isEnabled('ets_reviews') && (($display_error && !$error) || (!$display_error && $error))) {// [false|true|null|'error']
            $error = Module::getInstanceByName('ets_reviews')->checkValidityVoucher($this->code, $error, $context);// [''|'error']
            if (is_bool($error))
                return $error;
            if (is_string($error))
                return (!$display_error) ? false : $error;
        }
        /*end-ets_reviews*/

        /*begin-ets_abandonedcart*/
        if (Module::isEnabled('ets_abandonedcart') && (($display_error && !$error) || (!$display_error && $error))) {// [false|true|null|'error']
            $error = Module::getInstanceByName('ets_abandonedcart')->checkValidityVoucher($this->code, $error, $context);// [''|'error']
            if (is_bool($error))
                return $error;
            if (is_string($error))
                return (!$display_error) ? false : $error;
        }
        /*end-ets_abandonedcart*/

        if (is_bool($error))
            return $error;
        if (is_string($error))
            return (!$display_error) ? false : $error;

        return $error;
    }
}