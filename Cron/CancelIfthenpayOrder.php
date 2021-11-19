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

namespace Ifthenpay\Payment\Cron;

use Ifthenpay\Payment\Cron\IfthenpayCron;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Factory\Cancel\CancelIfthenpayOrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Api\PaymentMethodListInterface;


class CancelIfthenpayOrder extends IfthenpayCron {

    private $cancelIfthenpayOrderFactory;

    public function __construct(
        CancelIfthenpayOrderFactory $cancelIfthenpayOrderFactory,
        Gateway $gateway,
        IfthenpayLogger $logger,
        StoreManagerInterface $storeManager,
        PaymentMethodListInterface $paymentMethodList
    ) {
        parent::__construct($gateway, $logger, $storeManager, $paymentMethodList);
        $this->cancelIfthenpayOrderFactory = $cancelIfthenpayOrderFactory;
    }

    public function execute(): void
    {
        try {
            $this->logger->debug('Cron cancel Ifthenpay order started');
            foreach ($this->getActivatedPaymentMethod() as $paymentMethod) {
                if ($this->gateway->checkIfthenpayPaymentMethod($paymentMethod->getCode()) &&
                    in_array($paymentMethod->getCode(), $this->gateway->getPaymentMethodsCanCancel())) {
                        $this->logger->debug('Cron cancel Ifthenpay ' . $paymentMethod->getCode() .  ' order started');
                        $this->cancelIfthenpayOrderFactory->setType($paymentMethod->getCode())->build()->cancelOrder();
                        $this->logger->debug('Cron cancel Ifthenpay ' . $paymentMethod->getCode() .  ' order ended');
                }
            }
            $this->logger->debug('Cron cancel Ifthenpay order executed with success');
        } catch (\Throwable $th) {
            $this->logger->debug('Error Cron cancel Ifthenpay order', [
                'error' => $th,
                'errorMessage' => $th->getMessage()
            ]);
            throw $th;
        }
    }
}
