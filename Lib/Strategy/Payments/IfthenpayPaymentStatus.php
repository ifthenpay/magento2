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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Strategy\Payments;

use Ifthenpay\Payment\Lib\Factory\Payment\PaymentChangeStatusFactory;

class IfthenpayPaymentStatus
{
    private $paymentMethod;

    public function __construct(
        PaymentChangeStatusFactory $factory
    )
    {
        $this->factory = $factory;
    }
    public function execute(): void
    {
        $this->factory
            ->setType($this->paymentMethod)
            ->build()
            ->changePaymentStatus();
    }

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}
