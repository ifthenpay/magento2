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

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Strategy\Payments\IfthenpayPaymentStatus;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Api\PaymentMethodListInterface;

class CheckIfthenpayPaymentStatus extends IfthenpayCron {

    private $ifthenpayPaymentStatus;


    public function __construct(
        IfthenpayPaymentStatus $ifthenpayPaymentStatus,
        Gateway $gateway,
        IfthenpayLogger $logger,
        StoreManagerInterface $storeManager,
        PaymentMethodListInterface $paymentMethodList
    ) {
        parent::__construct($gateway, $logger, $storeManager, $paymentMethodList);
        $this->ifthenpayPaymentStatus = $ifthenpayPaymentStatus;
        $this->storeManager = $storeManager;
        $this->paymentMethodList = $paymentMethodList;
    }

    public function execute(): void
    {
        try {
            foreach ($this->getActivatedPaymentMethod() as $paymentMethod) {
                if ($this->gateway->checkIfthenpayPaymentMethod($paymentMethod->getCode())) {
                    $this->logger->debug('Cron check payment status ' . $paymentMethod->getCode() .  ' started');
                    $this->ifthenpayPaymentStatus->setPaymentMethod($paymentMethod->getCode())->execute();
                    $this->logger->debug('Cron check payment status ' . $paymentMethod->getCode() .  ' ended');
                }
            }
            $this->logger->debug('Cron check Ifthenpay payment status');
        } catch (\Throwable $th) {
            $this->logger->debug('Error Cron check Ifthenpay payment status', ['error' => $th, 'errorMessage' => $th->getMessage()]);
            throw $th;
        }
    }
}
