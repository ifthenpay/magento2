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
use Ifthenpay\Payment\Lib\Services\CcardService;
use Ifthenpay\Payment\Lib\Utility\Currency;


class CcardTxnIdHandler implements HandlerInterface
{
    private $ccardService;
    private $currency;

    public function __construct(
        CcardService $ccardService,
        Currency $currency
    ) {
        $this->ccardService = $ccardService;
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


        $requestId = $response['RequestId'];

        $payment->setTransactionId($requestId);

        $payment->setAdditionalInformation("requestId", $requestId);
        $payment->setAdditionalInformation("paymentUrl", $response['PaymentUrl']);
        $payment->setAdditionalInformation("orderId", $orderId);
        $payment->setAdditionalInformation("orderTotal", $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::MBWAY);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);


        // save to ifthenpay_ccard table
        $this->ccardService->setData(
            [
                "request_id" => $requestId,
                "order_id" => $orderId,
                "order_total" => (string) $convertedOrderTotal,
                "status" => 'pending'
            ]
        );

        $this->ccardService->save();

    }
}
