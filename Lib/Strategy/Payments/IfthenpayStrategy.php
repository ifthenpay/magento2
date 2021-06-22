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


use Magento\Checkout\Model\Session;
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentReturnFactory;

class IfthenpayStrategy
{
    protected $paymentDefaultData;
    protected $order;
    protected $paymentValueFormated;
    protected $factory;
    protected $configData;
    protected $checkoutSession;
    protected $payment;



    public function __construct(
        DataBuilder $paymentDataBuilder,
        PaymentReturnFactory $factory,
        Session $checkoutSession
    )
    {
        $this->paymentDefaultData = $paymentDataBuilder;
        $this->factory = $factory;
        $this->checkoutSession = $checkoutSession;
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrder($this->order);
        $this->paymentDefaultData->setPaymentMethod($this->payment ? $this->payment->getMethod() : $this->order->getPayment()->getMethod());
    }

    public function setOrder($order): self
    {
        $this->order = $order;
        return $this;
    }

    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }
}
