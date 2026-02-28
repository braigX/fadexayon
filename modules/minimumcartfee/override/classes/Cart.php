<?php

class Cart extends CartCore
{
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false,
        $fee_payment = false,
        $only_cart = false
    ) {
        $module = Module::getInstanceByName('minimumcartfee');

        try {
            // ➤ First calculate the normal total (with discounts applied)
            $total = parent::getOrderTotal($withTaxes, $type, $products, $id_carrier, $use_cache, $keepOrderPrices);

            // ➤ Return immediately if only cart items or if partial type requested
            if ($only_cart || $type !== Cart::BOTH) {
                return $total;
            }

            if (!Validate::isLoadedObject($module)) {
                return $total;
            }

            // ➤ Calculate your minimum cart fee separately
            $fee = (float) $module->calculateMinimumCartFee($this);

            if ($fee_payment) {
                return $fee; // Specific case if only asking for the fee
            }
            if ($fee > 0) {
                $finalTotal = $total + $fee;
                return $finalTotal;
            }

            return $total;

        } catch (Exception $e) {
            return parent::getOrderTotal($withTaxes, $type, $products, $id_carrier, $use_cache, $keepOrderPrices);
        }
    }
}
