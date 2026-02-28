<?php

/**
 * Copyright © Lyra Network and contributors.
 * This file is part of Sogecommerce plugin for Prestashop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

use Lyranetwork\Sogecommerce\Sdk\Refund\Processor as RefundProcessor;
use Lyranetwork\Sogecommerce\Sdk\Form\Response as SogecommerceResponse;

class SogecommerceRefundProcessor implements RefundProcessor
{
    protected $sogecommerce;
    protected $context;

    public function __construct()
    {
        $this->sogecommerce = new Sogecommerce();
        $this->context = $this->sogecommerce->getContext();
    }

    public function doOnError($errorCode, $message)
    {
        // Allow offline refund and display warning message.
        $this->context->cookie->sogecommerceRefundWarn = $message;
    }

    /**
     * Action to do after a successful refund process.
     *
     * @throws Exception
     */
    public function doOnSuccess($operationResponse, $operationType)
    {
        // Retrieve Order from its Id.
        $cartId = (int) $operationResponse['orderDetails']['orderId'];
        $orderId = Order::getOrderByCartId($cartId);

        if (! $orderId) {
            return;
        }

        $order = new Order($orderId);

        // Retrieve order currency.
        $orderCurrency = new Currency((int) $order->id_currency);
        $currency = Lyranetwork\Sogecommerce\Sdk\Form\Api::findCurrencyByAlphaCode($orderCurrency->iso_code);

        // Total amount paid by the client.
        $orderAmount = Tools::ps_round($order->total_paid, $currency->getDecimals());
        $orderAmountInCents = $currency->convertAmountToInteger($orderAmount);

        $orderDetails = OrderDetail::getList($orderId);
        $orderRefundedAmount = 0;
        foreach($orderDetails as $orderDetail){
            // Retrieve the amount already refunded in PrestaShop.
            if (version_compare(_PS_VERSION_, '1.6', '<=')) {
                $orderRefundedAmount += Tools::ps_round($orderDetail["product_quantity_refunded"] * $orderDetail["total_price_tax_incl"], $currency->getDecimals());
            } else {
                $orderRefundedAmount += Tools::ps_round($orderDetail["total_refunded_tax_incl"], $currency->getDecimals());
            }
        }

        $orderRefundedAmountInCents = $currency->convertAmountToInteger($orderRefundedAmount);

        // Sum of refund request amount and amount already refunded in PrestaShop.
        $refundAmount = $operationResponse['amount'] + $orderRefundedAmountInCents;

        // Amount refunded on the Back Office.
        $transRefundedAmount = 0;
        if (isset($operationResponse['refundedAmount']) && $operationResponse['refundedAmount']) {
            $transRefundedAmount = $operationResponse['refundedAmount'];
        }

        if (isset($operationResponse['refundedAmountMulti']) && $operationResponse['refundedAmountMulti']) {
            $refundAmount = $operationResponse['refundedAmountMulti'] + $orderRefundedAmountInCents;
        }

        switch ($operationType) {
            case 'frac_update':
                if ($transRefundedAmount == $refundAmount && $refundAmount == $orderAmountInCents) {
                    $this->context->cookie->sogecommerceSplitPaymentUpdateRefundStatus = "True";
                    $order->setCurrentState((int) Configuration::get('SOGECOMMERCE_OS_REFUNDED'));
                } elseif(! ($transRefundedAmount == $refundAmount && $transRefundedAmount < $orderAmountInCents)) {
                    $msg = sprintf($this->translate('Refund of split payment is not supported. Please, consider making necessary changes in %1$s Back Office.'), 'Sogecommerce');

                    throw new \Exception($msg);
                }

                return;

            case 'already_cancel':
                $this->context->cookie->sogecommerceRefundWarn = 'Transaction already cancelled on payment gateway.';
                $operationType = 'cancel';

                break;

            case 'already_refund':
                $this->context->cookie->sogecommerceRefundWarn = 'Transaction already refunded on payment gateway.';
                $operationType = 'refund';

                break;

            default:
                break;
        }

        $responseData = SogecommerceTools::convertRestResult($operationResponse);
        $response = new SogecommerceResponse($responseData, null, null, null);

        // Save refund transaction in PrestaShop.
        $this->sogecommerce->createMessage($order, $response);
        $this->sogecommerce->savePayment($order, $response, $operationType === 'cancel');

        $isManualUpdateRefundStatus = isset($this->context->cookie->sogecommerceManualUpdateToManagedRefundStatuses) && ($this->context->cookie->sogecommerceManualUpdateToManagedRefundStatuses === 'True');
        if (!$isManualUpdateRefundStatus && $refundAmount == $orderAmountInCents) {
            $order->setCurrentState((int) Configuration::get('SOGECOMMERCE_OS_REFUNDED'));
        }
    }

    /**
     * Action to do after failed refund process.
     *
     */
    public function doOnFailure($errorCode, $message)
    {
        $this->context->cookie->sogecommerceRefundWarn = $message;
        if (isset($this->context->cookie->sogecommerceManualUpdateToManagedRefundStatuses)
            && ($this->context->cookie->sogecommerceManualUpdateToManagedRefundStatuses === 'True')) {
            unset($this->context->cookie->sogecommerceManualUpdateToManagedRefundStatuses);
        }

        $this->doOnError($errorCode, $message);
    }

    /**
     * Log informations.
     *
     */
    public function log($message, $level)
    {
        switch ($level) {
            case "ERROR":
                SogecommerceTools::getLogger()->logError($message);
                break;

            case "WARNING":
                SogecommerceTools::getLogger()->logWarning($message);
                break;

            case "INFO":
                SogecommerceTools::getLogger()->logInfo($message);
                break;

            default:
                SogecommerceTools::getLogger()->log($message);
                break;
        }
    }

    /**
     * Translate given message.
     *
     */
    public function translate($message)
    {
        return $this->sogecommerce->l($message);
    }
}