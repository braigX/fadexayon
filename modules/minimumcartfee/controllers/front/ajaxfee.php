<?php

class MinimumcartfeeAjaxfeeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        $cart = Context::getContext()->cart;
        if (!Validate::isLoadedObject($cart)) {
            die(json_encode(['fee_incl_tax' => 0]));
        }

        $minAmount = (float) Configuration::get('MINCARTFEE_MIN_AMOUNT');
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        $fee = 0.0;
        $module = Module::getInstanceByName('minimumcartfee');
        if (Validate::isLoadedObject($module) && method_exists($module, 'calculateMinimumCartFee')) {
            $fee = (float) $module->calculateMinimumCartFee($cart);
        } else {
            // Fallback to old behavior if module instance is unavailable
            $fee = max(0, Tools::ps_round($minAmount - $cartTotal, 2));
        }

        echo json_encode([
            'fee_incl_tax' => $fee,
            'formatted'    => Tools::displayPrice($fee),
        ]);
        exit;
    }
}
