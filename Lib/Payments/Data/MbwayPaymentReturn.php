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

use Ifthenpay\Payment\Lib\Base\Payments\MbwayBase;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentReturnInterface;

class MbwayPaymentReturn extends MbwayBase implements PaymentReturnInterface
{
    public function getPaymentReturn(): PaymentReturnInterface
    {
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayDataBuilder,
            strval($this->paymentDefaultData->order->getIncrementId()),
            strval($this->paymentDefaultData->order->getGrandTotal())
        )->getData();
        $this->saveToDatabase();
        $this->setRedirectUrl();
        return $this;
    }
}
