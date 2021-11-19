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

use Magento\Sales\Model\Order\Invoice;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Base\CheckPaymentStatusBase;

class MultibancoChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::MULTIBANCO;

    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setEntidade($this->configData['entidade']);
        $this->gatewayDataBuilder->setSubEntidade($this->configData['subEntidade']);
        $this->logGatewayBuilderData();
    }

    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if ($this->pendingOrders->getSize()) {
            foreach ($this->pendingOrders as $order) {
                $multibancoPayment = $this->paymentRepository->getByOrderId((string) $order->getIncrementId());
                $this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
                $this->gatewayDataBuilder->setTotalToPay($order->getGrandTotal());
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $multibancoPayment['id']);
                    $this->updateOrderStatus($order, Invoice::CAPTURE_OFFLINE);
                    $this->logChangePaymentStatus($multibancoPayment);
                }
            }
            
        }
    }
}
