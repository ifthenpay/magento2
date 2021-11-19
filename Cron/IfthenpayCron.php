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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Api\PaymentMethodListInterface;

class IfthenpayCron {

    protected $gateway;
    protected $logger;
    private $storeManager;
    private $paymentMethodList;

    public function __construct(
        Gateway $gateway,
        IfthenpayLogger $ifthenpayLogger,
        StoreManagerInterface $storeManager,
        PaymentMethodListInterface $paymentMethodList
    ) {
        $this->gateway = $gateway;
        $this->logger = $ifthenpayLogger;
        $this->storeManager = $storeManager;
        $this->paymentMethodList = $paymentMethodList;
    }

    protected function getActivatedPaymentMethod(): array
    {
        return $this->paymentMethodList->getActiveList($this->storeManager->getStore()->getId());
    }
}
