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
use Ifthenpay\Payment\Lib\Base\Payments\MbwayBase;
use Ifthenpay\Payment\Lib\Base\CheckPaymentStatusBase;

class MbwayChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::MBWAY;

    protected function setGatewayDataBuilder(): void
    {
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setMbwayKey($this->configData['mbwayKey']);
        $this->logGatewayBuilderData();
    }

    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if ($this->pendingOrders->getSize()) {
            foreach ($this->pendingOrders as $order) {
                $mbwayPayment = $this->paymentRepository->getByOrderId((string) $order->getIncrementId());
                $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                    $this->updatePaymentStatus((string) $mbwayPayment['id']);
                    $this->updateOrderStatus($order, Invoice::CAPTURE_OFFLINE);
                    $this->logChangePaymentStatus($mbwayPayment);
                }
            }

        }
    }
}
