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

namespace Ifthenpay\Payment\Lib\Payments\Data;

use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payment\Lib\Base\Payments\CCardBase;

class CCardPaymentReturn extends CCardBase implements PaymentReturnInterface
{

    public function getPaymentReturn()
    {
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->order->getOrderIncrementId()),
            strval($this->paymentDefaultData->order->getGrandTotalAmount())
        )->toArray();
        $this->saveToDatabase();
        $this->setRedirectUrl(true, $this->paymentGatewayResultData['paymentUrl']);
        return $this;
    }
}