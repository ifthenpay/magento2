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
use Ifthenpay\Payment\Lib\Services\PayshopService;
use Ifthenpay\Payment\Lib\Utility\Currency;


class PayshopTxnIdHandler implements HandlerInterface
{
    private $serviceFactory;
    private $payshopService;
    private $currency;

    public function __construct(
        PayshopService $payshopService,
        Currency $currency
    ) {
        $this->payshopService = $payshopService;
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


        // format deadline date
        $deadline = $response['deadline'] != '' ? date('d-m-Y', strtotime($response['deadline'])) : '';


        $paymentData = [
            'reference' => $response['Reference'],
            'transaction_id' => $response['RequestId'],
            'deadline' => $deadline,
            "order_id" => $orderId,
            "status" => 'pending',
            "created_at" => $currentDateStr
        ];


        $payment->setAdditionalInformation('deadline', $deadline);
        $payment->setAdditionalInformation('reference', $response['Reference']);
        $payment->setAdditionalInformation('orderTotal', $convertedOrderTotal);
        $payment->setAdditionalInformation('currencySymbol', ConfigVars::CURRENCY_SYMBOL_EURO);
        $payment->setAdditionalInformation('paymentMethod', ConfigVars::PAYSHOP);


        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);

        // save to ifthenpay_mbway table
        $this->payshopService->setData($paymentData);
        $this->payshopService->save();
    }

}
