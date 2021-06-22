<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class TxnIdHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        $response = $response;

        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        if ($response['status'] === '0') {
            $payment->setAdditionalInformation('idPedido', $response['idPedido']);
            $payment->setAdditionalInformation('paymentUrl', $response['paymentUrl']);
            $payment->setAdditionalInformation('totalToPay', $response['totalToPay']);
            $payment->setTransactionId($response['idPedido']);
            $payment->setIsTransactionClosed(0);
        } else {
            $payment->setAdditionalInformation('error', __("The payment couldn't be processed at this time. Please try again later."));
            throw new LocalizedException(__("The payment couldn't be processed at this time. Please try again later."));
        }
    }
}
