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
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use InvalidArgumentException;
use Magento\Sales\Model\Order\Creditmemo;
use Ifthenpay\Payment\Logger\Logger;



class RefundHandler implements HandlerInterface
{
    private $serviceFactory;
    private $logger;


    const RESPONSE_CODE = 'Code';
    const RESPONSE_SUCCESS = '1';
    const RESPONSE_NO_FUNDS = '-1';
    const RESPONSE_ERROR = '0';

    public function __construct(
        ServiceFactory $serviceFactory,
        Logger $logger
    ) {
        $this->serviceFactory = $serviceFactory;
        $this->logger = $logger;
    }


    public function handle(array $handlingSubject, array $response)
    {
        if (
            !isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            $this->logger->error('gateway/response/refundhandler: Payment data object (handlingSubject[payment]) should be provided');
            throw new InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        if ($response[self::RESPONSE_CODE] == self::RESPONSE_SUCCESS) {
            $creditMemo = $payment->getCreditmemo();
            $creditMemo->setState(Creditmemo::STATE_REFUNDED);

        }
        if ($response[self::RESPONSE_CODE] == self::RESPONSE_NO_FUNDS || $response[self::RESPONSE_CODE] == self::RESPONSE_ERROR) {
            $creditMemo = $payment->getCreditmemo();
            $creditMemo->setState(Creditmemo::STATE_CANCELED);

            if ($response[self::RESPONSE_CODE] == self::RESPONSE_NO_FUNDS) {
                $this->logger->error('gateway/response/refundhandler: Unable to refund due to lack of funds.', [
                    'response' => $response
                ]);
                throw new \Exception(__('Error: Unable to refund due to lack of funds.'));
            }

            if ($response[self::RESPONSE_CODE] == self::RESPONSE_ERROR) {
                $this->logger->error('gateway/response/refundhandler: Refund request failed.', [
                    'response' => $response
                ]);
                throw new \Exception(__('Error: Refund request failed.'));
            }

        }

    }
}
