<?php

class MinimumcartfeeAjaxfeeModuleFrontController extends ModuleFrontController
{
    /** Absolute path of the log file (same dir as this controller) */
    private const LOG_FILE = __DIR__.'/minimumcartfee_ajaxfee.log';

    /**
     * Very small, dependency-free logger.
     * Writes one line per call:  [YYYY-MM-DD HH:MM:SS] message
     */
    private function logToFile(string $message): void
    {
        $ts = date('[Y-m-d H:i:s] ');
        // the @ suppresses warnings if the file cannot be created
        @file_put_contents(self::LOG_FILE, $ts.$message.PHP_EOL, FILE_APPEND);
    }

    public function initContent()
    {
        parent::initContent();
        header('Content-Type: application/json');

        $cart = Context::getContext()->cart;
        if (!Validate::isLoadedObject($cart)) {
            $this->logToFile('Cart not loaded – aborting');
            die(json_encode(['fee_incl_tax' => 0]));
        }

        $minAmount = (float) Configuration::get('MINCARTFEE_MIN_AMOUNT');
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);

        $this->logToFile("Cart total = {$cartTotal} | Min amount = {$minAmount}");

        $fee = max(0, Tools::ps_round($minAmount - $cartTotal, 2));
        $this->logToFile("Calculated fee = {$fee}");

        echo json_encode([
            'fee_incl_tax' => $fee,
            'formatted'    => Tools::displayPrice($fee),
        ]);
        exit;
    }
}
