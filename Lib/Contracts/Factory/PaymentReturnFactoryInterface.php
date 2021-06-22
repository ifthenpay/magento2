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

namespace Ifthenpay\Payment\Lib\Contracts\Factory;

use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentReturnInterface;

interface PaymentReturnFactoryInterface
{
    public function build(): PaymentReturnInterface;
}