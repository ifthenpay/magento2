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

namespace Ifthenpay\Payment\Lib\Traits\Admin;

trait CheckIfnotRenderElement
{
    protected function checkIfRenderElement(array $userPaymentMethods, bool $notInAray = true): bool
    {
        if (!in_array($this->paymentMethod, $userPaymentMethods)) {
            return true;
        } else {
            return false;
        }
    }
}
