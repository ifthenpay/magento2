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
use Ifthenpay\Payment\Lib\Services\PixService;
use Ifthenpay\Payment\Lib\Utility\Currency;


class PixTxnIdHandler implements HandlerInterface
{
    private $pixService;
    private $currency;

    public function __construct(
        PixService $pixService,
        Currency $currency
    ) {
        $this->pixService = $pixService;
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


        $transactionId = $response['requestId'];
        $hash = $response['hash'];

        $payment->setTransactionId($transactionId);
        $payment->setAdditionalInformation("transactionId", $transactionId);
        $payment->setAdditionalInformation("paymentUrl", $response['paymentUrl']);
        $payment->setAdditionalInformation("orderId", $orderId);
        $payment->setAdditionalInformation("orderTotal", $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::PIX);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);

        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $currentDateStr = $currentDate->format('Y-m-d H:i:s');

        // save to ifthenpay_pix table
        $this->pixService->setData(
            [
                "transaction_id" => $transactionId,
                "order_id" => $orderId,
                "hash" => $hash,
                "status" => 'pending',
                "created_at" => $currentDateStr
            ]
        );

        $this->pixService->save();

    }

}
