<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Services\IfthenpaygatewayService;
use Ifthenpay\Payment\Lib\Utility\Currency;


class IfthenpaygatewayTxnIdHandler implements HandlerInterface
{
    private $ifthenpaygatewayService;
    private $currency;

    public function __construct(
        IfthenpaygatewayService $ifthenpaygatewayService,
        Currency $currency
    ) {
        $this->ifthenpaygatewayService = $ifthenpaygatewayService;
        $this->currency = $currency;
    }

    public function handle(array $handlingSubject, array $response): void
    {
        if (
            !isset($handlingSubject['payment']) ||
            !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $orderId = $order->getOrderIncrementId();
        $currency = $order->getCurrencyCode();
        $orderTotal = $order->getGrandTotalAmount();
        $convertedOrderTotal = $this->currency->convertAndFormatToEuro($currency, $orderTotal);


        $paymentUrl = $response['RedirectUrl'];
        $deadline = $response['deadline'] != '' ? date('d-m-Y', strtotime($response['deadline'])) : '';
        $transactionId = substr(str_shuffle(bin2hex(random_bytes(10))), 0, 20);


        $payment->setTransactionId($transactionId);
        $payment->setAdditionalInformation("paymentUrl", $paymentUrl);
        $payment->setAdditionalInformation("deadline", $deadline);
        $payment->setAdditionalInformation("orderId", $orderId);
        $payment->setAdditionalInformation("orderTotal", $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::IFTHENPAYGATEWAY);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);

        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $currentDateStr = $currentDate->format('Y-m-d H:i:s');

        // save to ifthenpay_ifthenpaygateway table
        $this->ifthenpaygatewayService->setData(
            [
                "order_id" => $orderId,
                "transaction_id" => $transactionId,
                "status" => 'pending',
                "payment_url" => $paymentUrl,
                "deadline" => $deadline,
                "created_at" => $currentDateStr
            ]
        );



        $this->ifthenpaygatewayService->save();
    }
}
