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

namespace Ifthenpay\Payment\Lib\Contracts\Payments;

use Ifthenpay\Payment\Lib\Payments\PaymentStatus;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;

interface PaymentStatusInterface
{
    public function getPaymentStatus(): bool;
    public function setData(GatewayDataBuilder $data): PaymentStatus;
}
