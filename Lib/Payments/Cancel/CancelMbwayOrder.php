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

namespace Ifthenpay\Payment\Lib\Payments\Cancel;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Payments\Payment;
use Ifthenpay\Payment\Lib\Base\Payments\MbwayBase;
use Ifthenpay\Payment\Lib\Payments\Cancel\CancelOrder;

class CancelMbwayOrder extends CancelOrder {

    protected $paymentMethod = Gateway::MBWAY;

    public function cancelOrder(): void
    {
        try {
            if ($this->configData['cancelOrder']) {
                $this->setPendingOrders();
                if ($this->pendingOrders->getSize()) {
                    foreach ($this->pendingOrders as $order) {
                        $idPedido = $order->getPayment()->getAdditionalInformation('idPedido');
                        if ($idPedido) {
                            $this->setGatewayDataBuilderBackofficeKey();
                            $this->gatewayDataBuilder->setMbwayKey($this->configData['mbwayKey']);
                            $this->gatewayDataBuilder->setIdPedido((string) $order->getPayment()->getAdditionalInformation('idPedido'));
                            if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                                $this->checkTimeChangeStatus($order);
                            }
                        }
                        $this->logCancelOrder(Gateway::MBWAY, $idPedido, $order->getData());
                    };
                }
            }
        } catch (\Throwable $th) {
            $this->logErrorCancelOrder(Gateway::MBWAY, $th);
            throw $th;
        }
    }
}
