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
use Ifthenpay\Payment\Lib\Services\MultibancoService;
use Ifthenpay\Payment\Lib\Utility\Currency;


class MultibancoTxnIdHandler implements HandlerInterface
{
    private $serviceFactory;
    private $multibancoService;
    private $currency;

    public function __construct(
        MultibancoService $multibancoService,
        Currency $currency
    ) {
        $this->multibancoService = $multibancoService;
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

        // ExpiryDate

        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $orderId = $order->getOrderIncrementId();
        $currency = $order->getCurrencyCode();
        $orderTotal = $order->getGrandTotalAmount();
        $convertedOrderTotal = $this->currency->convertAndFormatToEuro($currency, $orderTotal);

        $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $currentDateStr = $currentDate->format('Y-m-d H:i:s');

        $paymentData = [
            "order_id" => $orderId,
            "status" => 'pending',
            "created_at" => $currentDateStr
        ];


        // for dynamic multibanco
        if (isset($response['RequestId']) && $response['RequestId'] != '') {

            $paymentData['reference'] = $response['Reference'];
            $paymentData['entity'] = $response['Entity'];
            $paymentData['request_id'] = $response['RequestId'];
            $paymentData['deadline'] = $response['ExpiryDate'];


            $payment->setAdditionalInformation("entity", $response['Entity']);
            $payment->setAdditionalInformation("reference", $response['Reference']);
            $payment->setAdditionalInformation("deadline", $response['ExpiryDate']);


            // for static entity multibanco
        } else {

            $reference = $this->generateStaticReference($orderId, $convertedOrderTotal, $response['entity'], $response['subEntity']);

            $paymentData['reference'] = $reference;
            $paymentData['entity'] = $response['entity'];

            $payment->setAdditionalInformation("entity", $response['entity']);
            $payment->setAdditionalInformation("reference", $reference);
        }

        $payment->setAdditionalInformation("orderId", $orderId);
        $payment->setAdditionalInformation("orderTotal", $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::MULTIBANCO);


        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);


        // save to ifthenpay_mbway table
        $this->multibancoService->setData($paymentData);
        $this->multibancoService->save();
    }


    /**
     * generate multibanco reference using the algorithm provided by ifthenpay
     * @param string $orderId
     * @param string $orderTotal
     * @param string $entity
     * @param string $subEntity
     * @return string
     */
    private function generateStaticReference(string $orderId, string $orderTotal, string $entity, string $subEntity): string
    {
        $orderId = "0000" . $orderId;

        if (strlen($subEntity) === 2) {
            //Apenas sao considerados os 5 caracteres mais a direita do order_id
            $seed = substr($orderId, (strlen($orderId) - 5), strlen($orderId));
            $chk_str = sprintf('%05u%02u%05u%08u', $entity, $subEntity, $seed, round($orderTotal * 100));
        } else {
            //Apenas sao considerados os 4 caracteres mais a direita do order_id
            $seed = substr($orderId, (strlen($orderId) - 4), strlen($orderId));
            $chk_str = sprintf('%05u%03u%04u%08u', $entity, $subEntity, $seed, round($orderTotal * 100));
        }
        $chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);
        $chk_val = 0;
        for ($i = 0; $i < 20; $i++) {
            $chk_int = substr($chk_str, 19 - $i, 1);
            $chk_val += ($chk_int % 10) * $chk_array[$i];
        }
        $chk_val %= 97;
        $chk_digits = sprintf('%02u', 98 - $chk_val);
        //referencia
        return $subEntity . $seed . $chk_digits;
    }
}
