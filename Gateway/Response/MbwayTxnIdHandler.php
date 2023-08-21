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
use Ifthenpay\Payment\Lib\Services\MbwayService;
use Ifthenpay\Payment\Lib\Utility\Currency;



class MbwayTxnIdHandler implements HandlerInterface
{
    private $mbwayService;
    private $currency;

    public function __construct(
        MbwayService $mbwayService,
        Currency $currency
    ) {
        $this->mbwayService = $mbwayService;
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


        $tansactionId = $response['IdPedido'];

        $payment->setTransactionId($response['IdPedido']);
        $payment->setAdditionalInformation("transactionId", $tansactionId);
        $payment->setAdditionalInformation("orderId", $orderId);
        $payment->setAdditionalInformation("orderTotal", $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::MBWAY);


        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);


        $phoneNumber = $payment->getAdditionalInformation('countryCode') . '#' . $payment->getAdditionalInformation('phoneNumber');

        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $currentDateStr = $currentDate->format('Y-m-d H:i:s');


        // save to ifthenpay_mbway table
        $this->mbwayService->setData(
            [
                "transaction_id" => $tansactionId,
                "phone_number" => $phoneNumber,
                "order_id" => $orderId,
                "order_total" => (string) $convertedOrderTotal,
                "status" => 'pending',
                "created_at" => $currentDateStr
            ]
        );

        $this->mbwayService->save();

    }
}
