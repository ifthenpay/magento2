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

use Ifthenpay\Payment\Lib\Strategy\Payments\IfthenpayStrategy;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentReturnInterface;


class IfthenpayPaymentReturn extends IfthenpayStrategy
{
    public function execute(): PaymentReturnInterface
    {
        $this->setDefaultData();

        return $this->factory
            ->setType($this->paymentDefaultData->getData()->paymentMethod)
            ->setPaymentDefaultData($this->paymentDefaultData)
            ->build()
            ->getPaymentReturn();
    }
}